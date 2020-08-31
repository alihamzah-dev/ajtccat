<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestAnswer extends Model
{
    protected $table = 'test_answer';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $fillable = ['test_segment_transact_id', 'question_item_id', 'order_code'];

    public function getSegmentItem()
    {
        return $this->belongsTo(\App\Models\SegmentItem::class, 'test_segment_transact_id');
    }

    public function getOrder()
    {
        return $this->belongsTo(\App\Models\Order::class, 'order_code');
    }
}
