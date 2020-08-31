<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DownloadVoucher implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Order::select('voucher','status','created_at')->get();
    }

    public function headings(): array{
        return [
            'Vocuher',
            'Status',
            'Created_at'
        ];
    }
}
