<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SegmentStamp extends Model
{
    protected $table = 'segment_stamp';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $fillable = ['type', 'type_id', 'order_code'];
}
