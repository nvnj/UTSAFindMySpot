<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Spot extends Model
{
    /** @use HasFactory<\Database\Factories\SpotFactory> */
    use HasFactory;

    protected $fillable = [
        'lot_id',
        'garage_id',
        'spot_number',
        'level',
        'occupied',
        'last_updated_at',
    ];

    protected function casts(): array
    {
        return [
            'lot_id' => 'integer',
            'garage_id' => 'integer',
            'level' => 'integer',
            'occupied' => 'boolean',
            'last_updated_at' => 'datetime',
        ];
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function garage(): BelongsTo
    {
        return $this->belongsTo(Garage::class);
    }

    public function cameraReports(): HasMany
    {
        return $this->hasMany(CameraReport::class);
    }

    public function gpsReports(): HasMany
    {
        return $this->hasMany(GpsReport::class);
    }
}
