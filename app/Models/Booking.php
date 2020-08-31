<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;
    protected $table = 'booking';
    protected $fillable = ['code','customer_name','qty','status'];

    public function getOrder(){
        return $this->hasMany('\App\Models\Order', DB::raw('count(test_bank_id) as total_bank'), 'booking_id')->groupBy('test_bank_id');
    }
}
