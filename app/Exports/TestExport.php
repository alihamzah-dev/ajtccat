<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\TestBank;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class TestExport implements FromView
{
    public function view(): View
    {
        $booking = Order::where('booking_id', request('bookingID'))->first();
        $result = TestBank::find($booking->test_bank_id);
        $new_result = [];
        $sql_query = [];
        $sql_query_stamp = [];
        $sql_query_stamp2 = [];
        if ($result) {
            foreach ($result->getSegment as $segment) {
                $sql_query_stamp[] = "if(a.sub_test='".$segment->sub_test."', min(c.start_time), null) ".strtolower(str_replace(' ', '_', $segment->sub_test))."_start_time, if(a.sub_test='".$segment->sub_test."', max(c.end_time), null) ".strtolower(str_replace(' ', '_', $segment->sub_test)).'_end_time';
                $sql_query_stamp2[] = 'max('.strtolower(str_replace(' ', '_', $segment->sub_test)).'_start_time) as '.strtolower(str_replace(' ', '_', $segment->sub_test)).'_start_time, max('.strtolower(str_replace(' ', '_', $segment->sub_test)).'_end_time) as '.strtolower(str_replace(' ', '_', $segment->sub_test)).'_end_time';
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
        // $result2 = [];
        $stamp = DB::table(DB::table('test_segment as a')
        ->join('test_segment_item as b', 'a.id', 'b.test_segment_id')
        ->join('segment_stamp as c', function ($join) {
            $join->on('b.id', 'c.type_id');
            $join->where('c.type', 'Segment Item');
        })
        ->select('c.order_code')
        ->selectRaw(implode(', ', $sql_query_stamp))
        ->groupBy('a.sub_test')
        ->orderBy('a.sort', 'asc')
        ->orderBy('a.id', 'asc')
        ->where('a.test_bank_id', $booking->test_bank_id)
        ->groupBy('c.order_code'))
        ->selectRaw('max(order_code) as order_code')
        ->selectRaw(implode(', ', $sql_query_stamp2))
        ->groupBy('order_code')
        ->get();
        // dd($stamp);
        return view('exports.test', [
            'header' => $new_result,
            'test' => $result2,
            'stamp' => $stamp,
        ]);
        exit;
    }
}
