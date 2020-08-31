<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionItem extends Model
{
    use SoftDeletes;

    protected $table = 'question_item';
    protected $dateFormat = 'Y-m-d H:i:s';
}
