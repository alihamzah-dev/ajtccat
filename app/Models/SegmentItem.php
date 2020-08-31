<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SegmentItem extends Model
{
    use SoftDeletes;

    protected $table = 'test_segment_item';
    protected $dateFormat = 'Y-m-d H:i:s';

    public function getSegment()
    {
        return $this->belongsTo(\App\Models\TestSegment::class, 'test_segment_id');
    }

    public function getQuestion()
    {
        return $this->belongsTo(\App\Models\Question::class, 'question_code', 'code');
    }

    public function getAnswer()
    {
        return $this->hasOne(\App\Models\TestAnswer::class, 'test_segment_item_id', 'id');
    }
}
