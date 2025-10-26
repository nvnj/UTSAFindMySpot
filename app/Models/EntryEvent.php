<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntryEvent extends Model
{
    /** @use HasFactory<\Database\Factories\EntryEventFactory> */
    use HasFactory;

    protected $fillable = [
        'garage_id',
        'action',
        'event_at',
    ];

    protected function casts(): array
    {
        return [
            'garage_id' => 'integer',
            'event_at' => 'datetime',
        ];
    }

    public function garage(): BelongsTo
    {
        return $this->belongsTo(Garage::class);
    }
}
