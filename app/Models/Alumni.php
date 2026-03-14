<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alumni extends Model
{
    use HasFactory;

    protected $table = 'alumni';

    public const STATUS_BELUM_DILACAK = 'Belum Dilacak';
    public const STATUS_TERIDENTIFIKASI = 'Teridentifikasi';
    public const STATUS_PERLU_VERIFIKASI = 'Perlu Verifikasi';
    public const STATUS_TIDAK_DITEMUKAN = 'Tidak Ditemukan';

    protected $fillable = [
        'nim',
        'nama_lengkap',
        'program_studi',
        'tahun_lulus',
        'email',
        'kota',
        'status_pelacakan',
    ];

    protected $casts = [
        'tahun_lulus' => 'integer',
    ];

    public function trackingResults(): HasMany
    {
        return $this->hasMany(TrackingResult::class);
    }
}