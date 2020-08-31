<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;

    protected $table = 'question';
    protected $dateFormat = 'Y-m-d H:i:s';

    public function getQuestionItem()
    {
        return $this->hasMany(\App\Models\QuestionItem::class, 'question_code', 'code');
    }
}
