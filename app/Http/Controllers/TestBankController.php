<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestBankController extends Controller
{
    public function all(){
        return \App\Models\TestBank::all();
    }

    public function show($id){
        return \App\Models\TestBank::with('getSegment.getSegmentItem.getQuestion')->find($id);
    }

    public function get_segment($id){
        return \App\Models\TestSegment::where('test_bank_id', $id)->get();
    }

    public function get_segment_item($id){
        return \App\Models\TestSegmentItem::find($id);
    }

    public function store(Request $request){    
        $question = \App\Models\Question::whereIn('id', $request["segment_id"])->get();
        $segment_id = $request["segment_id"];
        $bank = new \App\Models\TestBank();
        $bank->code = "TB00" . $this->generateRandomString(3);
        $bank->name = $request["bank_name"];
        $bank->status = "Not Active";
        $bank->save();
        foreach($question as $val){
            $segment = new \App\Models\TestSegment();
            $segment->test_bank_id = $bank->id;
            $segment->description = $val->getSubTest["description"] ?? '';
            $segment->broad = $val["broad"];
            $segment->narrow = $val["narrow"];
            $segment->sub_test = $val["sub_test"];
            $segment->sort = $request["sort"];
            $segment->save();
        }
        return $bank;
    }

    public function update($id, Request $request){
        $bank = \App\Models\TestBank::findOrFail($id);
        $bank->id = $id;
        $bank->code = "TB00TEST";
        $bank->name = $request["bank_name"];
        $bank->save();
        return $bank;
    }
    public function insert_segment_item(Request $request){
        $test_segment_id = $request["test_segment_id"];
        $code = $request["code"];
        foreach($code as $key => $val){
            $item = new \App\Models\TestSegmentItem();
            $item->test_segment_id = $test_segment_id;
            $item->question_code = $code[$key];
            $item->save();
        }
        return response()->json($item, 200);
    }

    public function alltest(){
        return \App\Models\AllTest::all();
    }

    public function category_item($id){
        return DB::table('test_segment_item as a')
                    ->select('a.test_segment_id','a.question_code','b.answer_key','b.answer_value','a.id')
                    ->leftJoin('question as b','a.question_code','b.code')
                    ->where('a.test_segment_id', $id)
                    ->get();
    }

    public function totaltest(){
        $total = \App\Models\AllTest::all();
        return [
            'totaltest' => $total->count()
        ];
    }

    public function get_question(){
        DB::statement(DB::raw('set @row:=0'));
        $question = \App\Models\Question::selectRaw('narrow, id, @row:=@row+1 as number')
        ->groupBy('narrow')
        ->orderBy('id','asc')
        ->get();
        return $question;
    }

    public function get_segment_category($id = false){
        return \App\Models\TestSegment::where('id', $id)->first();
    }

    public function get_question_category($sub_test = false){
        return \App\Models\Question::where('sub_test', $sub_test)->get();
    }

    public function total(){
        $total = \App\Models\TestBank::all();
        return [
            'totalbank' => $total->count()
        ];
    }

    public function delete($id){
        $check = \App\Models\TestBank::find($id);
        if($check){
            \App\Models\TestBank::find($id)->delete();
            \App\Models\TestSegment::where('test_bank_id', $id)->delete();
            return 204;
        }else{
            return response()->json(false, 200);
        }
        
    }

    public function delete_segment_item($id){
        \App\Models\TestSegmentItem::find($id)->delete();
        return 204;
    }

    private function generateRandomString($length){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return strtoupper($randomString);
    }
}
