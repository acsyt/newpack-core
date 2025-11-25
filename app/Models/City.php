<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    protected $fillable = ['state_id', 'name', 'active'];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * @return HasMany
     */
    public function zipCodes(): HasMany
    {
        return $this->hasMany(ZipCode::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

}
