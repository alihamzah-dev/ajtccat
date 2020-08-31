<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'order';
    protected $dateFormat = 'Y-m-d H:i:s';

    public function getTestBank()
    {
        return $this->belongsTo(\App\Models\TestBank::class, 'test_bank_id');
    }

    public function getBooking()
    {
        return $this->belongsTo(\App\Models\Booking::class, 'booking_id');
    }

    public function getAnswer()
    {
        return $this->hasMany(\App\Models\TestAnswer::class, 'order_code', 'code');
    }

    public function getStep()
    {
        return $this->hasOne(\App\Models\TestStep::class, 'order_code', 'code')->latest();
    }

    public function getStamp()
    {
        return $this->hasMany(\App\Models\SegmentStamp::class, 'order_code', 'code')->orderBy('id');
    }
}
