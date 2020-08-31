<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestStep extends Model
{
    protected $table = 'test_step';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $fillable = ['order_code', 'step', 'step_item', 'route'];
}
