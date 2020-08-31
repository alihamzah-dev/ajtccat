<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class TestBank extends Model
{
    use SoftDeletes, HasTranslations;

    protected $table = 'test_bank';
    protected $dateFormat = 'Y-m-d H:i:s';
    public $translatable = ['description'];

    public function getSegment()
    {
        return $this->hasMany(\App\Models\TestSegment::class, 'test_bank_id', 'id')->orderByRaw('-sort DESC, id');
    }
}
