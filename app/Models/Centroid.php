<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Centroid extends Model
{
    protected $table = 'centroids';
    protected $fillable = [
        'iteration',
        'cluster_number',
        'centroid_modal',
        'centroid_penghasilan'
    ];
}
