<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubTest extends Model
{
    use SoftDeletes;

    // protected $table = 'subtest';
    protected $table = 'sub_test_intro';

    public function getCluster()
    {
        return $this->belongsTo(\App\Models\Cluster::class, 'cluster_id', 'id');
    }
}
