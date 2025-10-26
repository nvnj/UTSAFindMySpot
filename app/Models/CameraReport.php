<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CameraReport extends Model
{
    /** @use HasFactory<\Database\Factories\CameraReportFactory> */
    use HasFactory;

    protected $fillable = [
        'lot_id',
        'spot_id',
        'occupied',
        'camera_id',
        'reported_at',
    ];

    protected function casts(): array
    {
        return [
            'lot_id' => 'integer',
            'spot_id' => 'integer',
            'occupied' => 'boolean',
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
