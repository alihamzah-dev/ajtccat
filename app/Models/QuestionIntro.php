<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionIntro extends Model
{
    use SoftDeletes;

    protected $table = 'question_intro';
    protected $dateFormat = 'Y-m-d H:i:s';

    public function getIntroAttribute()
    {
        return $this->attributes['intro'];
    }
}
