<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'last_name',
        'email',

        'phone',
        'phone_secondary',
        'mobile',
        'whatsapp',

        'suburb_id',
        'street',
        'exterior_number',
        'interior_number',
        'address_reference',

        'rfc',
        'razon_social',
        'regimen_fiscal',
        'uso_cfdi',

        'status',
        'client_type',

        'email_verified_at',
        'email_verification_token',

        'notes',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = [
        'email_verification_token',
    ];

    public function suburb(): BelongsTo
    {
        return $this->belongsTo(Suburb::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }


    public function fullName(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => Str::of($attributes['name'] ?? '')
                ->append(' ', $attributes['last_name'] ?? '')
                ->squish()
                ->title()
                ->toString()
        );
    }


    public function fullAddress(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                if (!$this->suburb_id || !$this->suburb) return null;

                $parts = array_filter([
                    $this->street,
                    $this->exterior_number ? "Ext. {$this->exterior_number}" : null,
                    $this->interior_number ? "Int. {$this->interior_number}" : null,
                    $this->suburb->name ?? null,
                    $this->suburb->zipCode->name ?? null,
                    $this->suburb->zipCode->city->name ?? null,
                    $this->suburb->zipCode->city->state->name ?? null,
                ]);

                return implode(', ', $parts);
            }
        );
    }


    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }


    public function scopeOfType($query, string $type)
    {
        return $query->where('client_type', $type);
    }


    public function scopeSearch($query, ?string $search)
    {
        if (!$search) return $query;

        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('rfc', 'like', "%{$search}%");
        });
    }
}
