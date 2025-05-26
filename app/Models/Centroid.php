<?php

namespace App\Models;

use App\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Model;

class Centroid extends Model
{
    protected $table = 'centroids';
    protected $casts = [
        'centroid_modal' => MoneyCast::class,
        'centroid_penghasilan' => MoneyCast::class,
    ];
    protected $fillable = [
        'iteration',
        'cluster_number',
        'centroid_modal',
        'centroid_penghasilan'
    ];
}
