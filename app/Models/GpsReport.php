<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GpsReport extends Model
{
    /** @use HasFactory<\Database\Factories\GpsReportFactory> */
    use HasFactory;

    protected $fillable = [
        'lot_id',
        'spot_id',
        'parked',
        'latitude',
        'longitude',
        'reported_at',
    ];

    protected function casts(): array
    {
        return [
            'lot_id' => 'integer',
            'spot_id' => 'integer',
            'parked' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'reported_at' => 'datetime',
        ];
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function spot(): BelongsTo
    {
        return $this->belongsTo(Spot::class);
    }
}
