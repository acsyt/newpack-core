<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Process extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'applies_to_pt',
        'applies_to_mp',
        'applies_to_compounds',
    ];

    protected $casts = [
        'applies_to_pt' => 'boolean',
        'applies_to_mp' => 'boolean',
        'applies_to_compounds' => 'boolean',
    ];

    public function scopeSearch($query, ?string $search)
    {
        if (!$search) return $query;

        return $query->where(function ($q) use ($search) {
            $q->where('code', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%");
        });
    }
}
