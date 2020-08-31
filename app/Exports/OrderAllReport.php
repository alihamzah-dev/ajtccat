<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderAllReport implements FromCollection, WithHeadings
{
    protected $booking_id;

    function __construct($booking_id){
        $this->booking_id = $booking_id;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Order::where('booking_id', $this->booking_id)
        ->where('status','Completed')
        ->get();
    }

    public function headings(): array{
        return [
            'No',
            'Booking Id',
            'Test Bank Id',
            'Code',
            'Full Name',
            'Nik',
            'Birth Place',
            'Birth Date',
            'Gender',
            'Education',
            'Status Education',
            'Institution',
            'Grade',
            'Mother Education',
            'Father Education',
            'Mother Job',
            'Father Job',
            'Test Date',
            'Voucher',
            'Score Iq',
            'Status',
            'Is Process',
            'Created At',
            'Updated At',
            'Report Pdf'
        ];
    }
}
