<?php

namespace App\Models;

use App\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Model;

class Umkm extends Model
{
    protected $table = 'umkms';
    protected $casts = [
        'modal' => MoneyCast::class,
        'penghasilan' => MoneyCast::class,
    ];
    protected $fillable = [
        'nama_umkm',
        'nama_pemilik',
        'alamat',
        'jenis_umkm',
        'modal',
        'penghasilan'
    ];
}
