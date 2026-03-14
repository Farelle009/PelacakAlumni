<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackingResult extends Model
{
    use HasFactory;

    public const STATUS_KEMUNGKINAN_KUAT = 'Kemungkinan Kuat';
    public const STATUS_PERLU_VERIFIKASI = 'Perlu Verifikasi';
    public const STATUS_TIDAK_COCOK = 'Tidak Cocok';
    public const STATUS_SEDANG_DILACAK = 'sedang_dilacak';

    protected $fillable = [
        'alumni_id',
        'tracking_source_id',
        'query',
        'judul',
        'snippet',
        'url',
        'nama_terdeteksi',
        'afiliasi',
        'jabatan',
        'lokasi',
        'confidence_score',
        'status_verifikasi',
        'tanggal_ditemukan',
    ];

    protected $casts = [
        'confidence_score' => 'float',
        'tanggal_ditemukan' => 'datetime',
    ];

    public function alumni(): BelongsTo
    {
        return $this->belongsTo(Alumni::class);
    }

    public function trackingSource(): BelongsTo
    {
        return $this->belongsTo(TrackingSource::class);
    }
}