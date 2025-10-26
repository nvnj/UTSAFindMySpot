<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lot extends Model
{
    /** @use HasFactory<\Database\Factories\LotFactory> */
    use HasFactory;

    protected $fillable = [
        'lot_code',
        'name',
        'location',
        'latitude',
        'longitude',
        'total_spots',
        'available_spots',
        'type',
        'space_type',
        'allowed_permits',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'total_spots' => 'integer',
            'available_spots' => 'integer',
            'allowed_permits' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function spots(): HasMany
    {
        return $this->hasMany(Spot::class);
    }

    public function cameraReports(): HasMany
    {
        return $this->hasMany(CameraReport::class);
    }

    public function gpsReports(): HasMany
    {
        return $this->hasMany(GpsReport::class);
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    public function getOccupancyPercentageAttribute(): float
    {
        if ($this->total_spots === 0) {
            return 0;
        }

        return round((($this->total_spots - $this->available_spots) / $this->total_spots) * 100, 2);
    }

    public function allowsPermit(?string $permitType): bool
    {
        if (! $permitType) {
            return true; // No filter, show all
        }

        $permitService = app(\App\Services\ParkingPermitService::class);
        $isAfterHours = $permitService->isAfterHours();
        $allowedSpaceTypes = $permitService->getAllowedSpaceTypes($permitType, $isAfterHours);

        // Check if this lot's space type is in the allowed list
        return in_array($this->space_type, $allowedSpaceTypes);
    }
}
