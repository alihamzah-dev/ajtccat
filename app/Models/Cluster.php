<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cluster extends Model
{
    use SoftDeletes;

    protected $table = 'cluster';

    public function getSubtest()
    {
        return $this->hasMany(\App\Models\SubTest::class, 'cluster_id', 'id');
    }
}
