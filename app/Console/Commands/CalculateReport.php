<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CalculateReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CalculateReport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $subtest = \App\Models\SubTest::all();
        $question = \App\Models\Question::all();
        $orders = \App\Models\Order::where('id', 725)
        ->where('status', 'Completed')
        ->where('is_process', '0')
        ->get();
        if ($orders) {
            foreach ($orders as $order) {
                $segments = $order->getTestBank->getSegment;
                if ($segments) {
                    $num_of_true = [];
                    foreach ($segments as $segment) {
                        $items = $segment->getSegmentItem;
                        if ($items) {
                            foreach ($items as $item) {
                                $answer = $order->getAnswer->where('test_segment_item_id', $item->id)->first();
                                $question_keys = $question->where('code', $item->question_code)->first();
                                if (strtolower($answer->value) == strtolower($question_keys->answer_key)) {
                                    @++$num_of_true[$question_keys->subtest_id];
                                }
                            }
                        }
                    }
                    $data = [];
                    $index = 0;
                    foreach ($num_of_true as $subtest_id => $count) {
                        $data[$index]['order_code'] = $order->code;
                        $data[$index]['subtest_id'] = $subtest_id;
                        $data[$index]['num_of_true'] = $count;
                        $w_score = \App\Models\SubstestWScore::where('subtest_id', $subtest_id)
                        ->where('score', $count)->first();
                        $data[$index]['w_score'] = $w_score->w_score ?? 0;
                        $age = \Carbon\Carbon::createFromDate($order->birth_date)->diff($order->test_date);
                        $age_in_month = ($age->format('%y') * 12) + $age->format('%m');
                        $deviasi = \App\Models\StandarDeviasi::where('age_in_month', '>=', $age_in_month)
                        ->orderBy('age_in_month')
                        ->first();
                        $data[$index]['mean_score'] = $deviasi->mean_value;
                        $data[$index]['sd_score'] = $deviasi->sd_value;
                        $data[$index]['score_value'] = ((($data[$index]['w_score'] - $data[$index]['mean_score']) * $data[$index]['sd_score']) + 100);
                        ++$index;
                    }
                    $nilai_subtest_insert = \App\Models\NilaiSubtest::insert($data);
                    if ($nilai_subtest_insert) {
                        $subtest_list = $subtest->whereIn('id', array_column($data, 'subtest_id'))->groupBy('subtest_id')->first()->toArray();
                        $clusters = \App\Models\Cluster::whereIn('id', array_column($subtest_list, 'cluster_id'))->get();
                        $i = 1;
                        foreach ($clusters as $cluster) {
                            $nilai_subtest = \App\Models\NilaiSubtest::whereIn('subtest_id', $cluster->getSubtest->pluck('id'))
                        ->where('order_code', $order->code)
                        ->get();
                            $data2 = [];
                            $data2['order_code'] = $order->code;
                            $data2['cluster_id'] = $cluster->id;
                            $data2['score'] = $nilai_subtest->pluck('num_of_true')->sum() / $nilai_subtest->pluck('num_of_true')->count();
                            $data2['w_score'] = $nilai_subtest->pluck('w_score')->sum() / $nilai_subtest->pluck('w_score')->count();
                            $data2['mean_score'] = $nilai_subtest->pluck('mean_score')->sum() / $nilai_subtest->pluck('mean_score')->count();
                            $data2['sd_score'] = $nilai_subtest->pluck('sd_score')->sum() / $nilai_subtest->pluck('sd_score')->count();
                            $data2['score_value'] = $nilai_subtest->pluck('score_value')->sum() / $nilai_subtest->pluck('score_value')->count();
                            $nilai_cluster_data = \App\Models\NilaiCluster::insert($data2);
                            ++$i;
                        }
                    }
                }
                if ($nilai_cluster_data) {
                    $nilai_cluster = \App\Models\NilaiCluster::where('order_code',$order->code)->get();
                    $order->score_iq = $nilai_cluster->pluck('score_value')->sum() / $nilai_cluster->pluck('score_value')->count();
                }
                $order->is_process = 1;
                $order->save();
            }
        }
    }
}
