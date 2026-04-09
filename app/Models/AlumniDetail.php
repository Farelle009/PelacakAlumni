<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlumniDetail extends Model
{
    use HasFactory;

    protected $table = 'alumni_details';

    protected $fillable = [
        'nama',
        'nim',
        'nama_pt',
        'kode_pt',
        'prodi',
        'kode_prodi',
        'jenjang',
        'jenis_kelamin',
        'status',
        'tanggal_masuk',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
    ];
}