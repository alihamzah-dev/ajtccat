<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Exports\DownloadVoucher;
use App\Exports\OrderAllReport;
use App\Exports\OrderReportByCode;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\OrderImport;
use Illuminate\Support\Arr;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = \App\Models\Booking::whereNull('deleted_at')->pluck('code');
        if($data->contains('BKD' . $this->generateRandomString(2))){
            return response()->json(false, 200);
        }else{
            $qty = $request["qty"];
            $booking = new \App\Models\Booking();
            $booking->code = 'BKD' . $this->generateRandomString(4);
            $booking->customer_name = $request["customer_name"];
            $booking->status = 'Not Started';
            $booking->save();
            for($i = 0; $i < $qty; $i++){
                $check_order = \App\Models\Order::orderBy('id')->pluck('code');
                if($check_order->contains('AJT190000000' . $this->generateRandomString(2))){
                    return response()->json(false, 200);
                }else{
                    $order = new \App\Models\Order();
                    $order->booking_id = $booking->id;
                    $order->code = 'AJT190000000' . $this->generateRandomString(2);
                    $order->voucher = $this->generateRandomString(8);
                    $order->status = 'Not Started';
                    $order->save();
                }
            }
            return response()->json($booking, 200);
        }
    }

    public function insert_order(Request $request){
        $qty = $request["qty"];
        for($i = 0; $i < $qty; $i++){
            $check_order = \App\Models\Order::orderBy('id')->pluck('code');
            if($check_order->contains('AJT190000000' . $this->generateRandomString(2))){
                return response()->json(false, 200);
            }else{
                $order = new \App\Models\Order();
                $order->booking_id = $request["booking_id"];
                $order->code = 'AJT190000000' . $this->generateRandomString(2);
                $order->voucher = $this->generateRandomString(8);
                $order->status = 'Not Started';
                $order->save();
            }
        }
        return response()->json($order, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($code)
    {
        $booking = \App\Models\Booking::where('code', $code)->first();
        return response()->json([
            'booking' => $booking,
            'total_booking' => \App\Models\Order::where('booking_id', $booking->id)->count()
          ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($code)
    {
        $booking = \App\Models\Booking::where('code', $code)->first();
        $order = DB::table('order as a')
                    ->select('*', DB::raw('count(a.booking_id) as total_booking'))
                    ->where('a.booking_id', $booking->id)
                    ->groupBy('a.booking_id')
                    ->get();
        return response()->json(array('booking' => $booking, 'order' => $order));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $qty = $request["qty"];
        $check = \App\Models\Order::where('booking_id', $id)
                ->get();
        $booking = \App\Models\Booking::findOrFail($id);
        $booking->id = $id;
        $booking->customer_name = $request["customer_name"];
        $booking->save();
        $qty2 = $check->count();
        if($qty < $qty2){
            $quantity = $qty2 - $qty[$key];
            $order_id = $check->where('booking_id', $val);
            foreach($order_id as $key2 => $val2){
                if($quantity < $key2 + 1){
                    $delete = \App\Models\Order::where('id', $val2->id)->delete();
                    return response()->json($delete, 200);
                }
            }
        }else if($qty > $qty2){
            $quantity = $qty2 + $qty;
            for($i = 0; $i < $quantity; $i++){
                $order = new \App\Models\Order();
                $order->booking_id = $id;
                $order->code = 'AJT190000000' . $this->generateRandomString(2);
                $order->voucher = $this->generateRandomString(8);
                $order->save();
                return response()->json($order, 200);
            }
        }
        return response()->json($booking, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $booking = \App\Models\Booking::findOrFail($id);
        if($booking->status != 'Completed' && $booking->status != 'In Progress'){
            $delete = \App\Models\Booking::findOrFail($id)->delete();
            return response()->json($delete, 200);
        }else{
            return response()->json(false, 200);
        }
    }

    public function delete_order($id)
    {
        $check = \App\Models\Order::findOrFail($id);
        if($check->status != 'Completed' && $check->status != 'In Progress'){
            $order = \App\Models\Order::findOrFail($id)->delete();
            return response()->json($order, 200);
        }else{
            return response()->json(false, 200);
        }
    }

    public function all(){
        $booking = DB::table('booking as a')
                 ->select('a.id','a.code','a.customer_name','a.status', DB::raw('COUNT(b.booking_id) as qty'))
                 ->leftJoin('order as b','a.id','b.booking_id')
                 ->groupBy('b.booking_id')
                 ->whereNull('a.deleted_at')
                 ->get(); 
        $data = [];
        foreach($booking as $val){
            $datax["code"] = $val->code;
            $datax["customer_name"] = $val->customer_name;
            $datax["status"] = $val->status;
            $datax["qty"] = $val->qty;
            $datax["action"] = [
                'code' => $val->code,
                'status' => $val->status
            ];
            $data[] = $datax;
        }
        return $data;
    }

    public function order($code){
        $booking = \App\Models\Booking::where('code', $code)->first();
        $result = DB::table('order as a')
            ->select('a.*','b.name as test_bank_name')
            ->leftJoin('test_bank as b','a.test_bank_id','b.id')
            ->where('booking_id', $booking->id)
            ->get();   
        return $result;
    }

    public function order_count_status($code){
        $booking = \App\Models\Booking::where('code', $code)->first();
        $not_started = \App\Models\Order::where('status', 'Not Started')
                        ->where('booking_id', $booking->id)
                        ->count();
        $in_progress = \App\Models\Order::where('status', 'In Progress')
                        ->where('booking_id', $booking->id)
                        ->count();
        $void = \App\Models\Order::where('status', 'Void')
                        ->where('booking_id', $booking->id)
                        ->count();
        $completed = \App\Models\Order::where('status', 'Completed')
                        ->where('booking_id', $booking->id)
                        ->count();
        return response()->json(array('not_started' => $not_started, 'in_progress' => $in_progress, 'void' => $void, 'completed' => $completed), 200);
    }

    public function order_report($code){
        $booking = \App\Models\Booking::where('code', $code)->first();
        $result = DB::table('order as a')
            ->select('a.*','b.name as test_bank_name')
            ->leftJoin('test_bank as b','a.test_bank_id','b.id')
            ->where('a.status','=', 'Completed')
            ->where('a.booking_id', $booking->id)
            ->get();   
        return $result;
    }

    public function order_id($id){
        return \App\Models\Order::findOrFail($id);
    }

    public function countbooking(){
        $total = \App\Models\Booking::all();
        return [
            'totalbooking' => $total->count()
        ];
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

    public function download_voucher(){
        return Excel::download(new DownloadVoucher, 'voucher.xlsx');
    }

    public function download_order($booking_id = false){
        return Excel::download(new OrderAllReport($booking_id), 'order.xlsx');
    }

    public function download_order_by_code($code = false){
        return Excel::download(new OrderReportByCode($code), 'order_by_code.xlsx');
    }

    public function import_report(Request $request){
        $data = Excel::toArray(new OrderImport, request()->file('file')); 
        return collect(head($data))
            ->each(function ($row, $key) {
                DB::table('order')
                    ->where('id', $row['no'])
                    ->update(Arr::except($row, ['no']));
            });
    }
}
