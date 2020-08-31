<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestSegment extends Model
{
    use SoftDeletes;

    protected $table = 'test_segment';
    protected $dateFormat = 'Y-m-d H:i:s';

    public function getSegmentItem()
    {
        return $this->hasMany(\App\Models\SegmentItem::class, 'test_segment_id')->orderByRaw('-sort DESC, id');
    }

    public function getStamp()
    {
        return $this->hasMany(\App\Models\SegmentStamp::class, 'test_segment_id');
    }

    public function getQuestionIntro()
    {
        return $this->hasOne(\App\Models\QuestionIntro::class, 'narrow', 'narrow');
    }
}
