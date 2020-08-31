<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderReportByCode implements FromCollection, WithHeadings
{
    protected $code;

    function __construct($code){
        $this->code = $code;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Order::where('code', $this->code)->get();
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
            'Created At',
            'Updated At',
            'Report Pdf'
        ];
    }
}
