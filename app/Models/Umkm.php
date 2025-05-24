<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Umkm extends Model
{
    protected $table = 'umkms';
    protected $fillable = [
        'nama_umkm',
        'nama_pemilik',
        'alamat',
        'jenis_umkm',
        'modal',
        'penghasilan'
    ];
}
