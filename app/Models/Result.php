<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $table = 'results';
    protected $fillable = [
        'umkm_id',
        'final_cluster'
    ];

    public function umkm()
    {
        return $this->belongsTo(Umkm::class);
    }
}
