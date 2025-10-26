<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Garage extends Model
{
    /** @use HasFactory<\Database\Factories\GarageFactory> */
    use HasFactory;

    protected $fillable = [
        'garage_code',
        'name',
        'location',
        'latitude',
        'longitude',
        'levels',
        'total_spots',
        'available_spots',
        'space_type',
        'allowed_permits',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'levels' => 'integer',
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

    public function entryEvents(): HasMany
    {
        return $this->hasMany(EntryEvent::class);
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

        // Check if any garage space type matches
        $garageType = 'garage_'.strtolower($this->garage_code);

        return in_array($garageType, $allowedSpaceTypes) || in_array('garage', $allowedSpaceTypes);
    }
}
