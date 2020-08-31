<?php

namespace App\Http\Controllers;

use App\Exports\TestExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
    }

    public function excel()
    {
        return Excel::download(new TestExport(), 'TestReport.xlsx');
    }

    public function pdf()
    {
        $order = \App\Models\Order::where('code', request('test_code'))->first();
        $result = \App\Models\TestBank::find($order->test_bank_id);
        $new_result = [];
        $sql_query = [];
        if ($result) {
            foreach ($result->getSegment as $segment) {
                if ($segment) {
                    foreach ($segment->getSegmentItem as $segmentItem) {
                        if ($segment->sub_test == 'Perceptual') {
                            if ($segmentItem->getQuestion->getQuestionItem) {
                                foreach ($segmentItem->getQuestion->getQuestionItem as $questionItem) {
                                    $new_result[$segment->sub_test][] = $segmentItem->id.'.'.$questionItem->id;
                                    $sql_query[] = "max(if(a.test_segment_item_id='".$segmentItem->id."' and question_item_id='".$questionItem->id."',a.value,'')) as '".$segmentItem->id.'.'.$questionItem->id."'";
                                }
                            }
                        } else {
                            $new_result[$segment->sub_test][] = $segmentItem->id;
                            $sql_query[] = 'max(if(a.test_segment_item_id='.$segmentItem->id.",a.value,'')) as '".$segmentItem->id."'";
                        }
                    }
                }
            }
        }
        // dd(collect((object) $new_result));
        $result2 = DB::table('test_answer as a')
        ->join('order as b', 'a.order_code', 'b.code')
        ->selectRaw('a.order_code,max(b.full_name) as full_name,max(b.birth_place) as birth_place,max(b.birth_date) as birth_date,max(b.gender) as gender,max(b.education) as education,max(b.status_education) as status_education,max(b.institution) as institution,max(b.grade) as grade,max(b.mother_education) as mother_education,max(b.father_education) as father_education,max(b.mother_job) as mother_job,max(b.father_job) as father_job,max(b.voucher) as voucher')
        ->selectRaw(implode(', ', $sql_query))
        ->groupBy('a.order_code')
        ->orderBy('a.order_code')
        ->where('b.booking_id', request('bookingID'))
        ->get();
        // dd($result2);
        $clusters = DB::table('cluster as a')
        ->selectRaw('a.name, b.score_value')
        ->join('nilai_cluster as b', 'a.id', 'b.cluster_id')
        ->where('b.order_code', $order->code)
        ->get();
        $subtests = DB::table('subtest as a')
        ->selectRaw('a.slug, b.score_value')
        ->join('nilai_subtest as b', 'a.id', 'b.subtest_id')
        ->where('b.order_code', $order->code)
        ->get();
        $output['data'] = $order;
        $output['data']['iq'] = json_encode([$order->score_iq]);
        $output['data']['iq_label'] = '90';
        $output['data']['pakets_id'] = '2';
        $output['data']['equivalent_age'] = [];
        $output['data']['clusters'] = $clusters->pluck('name')->toJson();
        $output['data']['desc_fragmental'] = [];
        $output['data']['dynamic_report'] = 'dynamic_report';
        $output['data']['kode_nilai_subtest'] = $this->getColorSubtest($subtests)->toJson();
        $output['data']['range_nilai_subtest'] = ['min' => 80, 'max' => 160];
        $output['data']['kode_nilai_cluster'] = 'kode_nilai_cluster';
        $output['data']['range_nilai_cluster'] = ['min' => 80, 'max' => 140];
        $output['data']['iq_color'] = get_color_coded($order->score_iq);
        $output['data']['label_subtest'] = $subtests->pluck('slug')->map(function ($item, $key) {
            return strtoupper($item);
        })->toJson();
        $output['data']['nilai_subtest'] = $subtests->pluck('score_value')->toJson();
        $output['data']['label_cluster'] = $clusters->pluck('name')->toJson();
        $output['data']['kode_nilai_cluster'] = $this->getColorCluster($clusters)->toJson();
        $output['data']['nilai_cluster'] = $clusters->pluck('score_value')->toJson();
        $output['data']['iq_scale'] = json_encode([$order->score_iq]);

        return view('reports.pdf', $output);
    }

    private function getColorCluster($clusters)
    {
        $result = [];
        if ($clusters) {
            foreach ($clusters as $cluster) {
                $result[] = get_color_coded($cluster->score_value);
            }
        }

        return collect($result);
    }

    private function getColorSubtest($subtest)
    {
        $result = [];
        if ($subtest) {
            foreach ($subtest as $subt) {
                $result[] = get_color_coded($subt->score_value);
            }
        }

        return collect($result);
    }
}
