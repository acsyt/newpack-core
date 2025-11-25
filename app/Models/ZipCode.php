<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ZipCode extends Model
{
    protected $fillable = ['id', 'city_id', 'name'];
    public function suburbs(): HasMany
    {
        return $this->hasMany(Suburb::class)
            ->orderBy('name');
    }

    public function suburb()
    {
        return $this->suburbs()->first();
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

}
