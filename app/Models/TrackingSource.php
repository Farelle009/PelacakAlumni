<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrackingSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_sumber',
        'base_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function trackingResults(): HasMany
    {
        return $this->hasMany(TrackingResult::class);
    }
}