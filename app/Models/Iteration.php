<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Iteration extends Model
{
    protected $table = 'iterations';
    protected $fillable = [
        'iteration',
        'umkm_id',
        'distances',
        'assigned_cluster'
    ];

    public function umkm()
    {
        return $this->belongsTo(Umkm::class);
    }
}
