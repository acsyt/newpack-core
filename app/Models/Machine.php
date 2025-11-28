<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Machine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'process_id',
        'speed_mh',
        'speed_kgh',
        'circumference_total',
        'max_width',
        'max_center',
        'status',
    ];

    protected $casts = [
        'speed_mh'              => 'float',
        'speed_kgh'             => 'float',
        'circumference_total'   => 'float',
        'max_width'             => 'float',
        'max_center'            => 'float',
    ];

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    public function scopeSearch($query, ?string $search)
    {
        if (!$search) return $query;

        return $query->where(function ($q) use ($search) {
            $q->where('code', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%");
        });
    }
}
