<?php

namespace App\Models;

use App\StateMachines\SupplierStatusStateMachine;
use Asantibanez\LaravelEloquentStateMachines\Traits\HasStateMachines;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory, SoftDeletes, HasStateMachines;

    protected $fillable = [
        'company_name',
        'contact_name',
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
        'legal_name',
        'tax_system',
        'use_cfdi',

        'supplier_type',
        'payment_terms',
        'credit_limit',

        'status',

        'notes',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $stateMachines = [
        'status' => SupplierStatusStateMachine::class
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
        return $query->where('status', SupplierStatusStateMachine::ACTIVE);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('supplier_type', $type);
    }

    public function scopeSearch($query, ?string $search)
    {
        if (!$search) return $query;

        return $query->where(function ($q) use ($search) {
            $q->where('company_name', 'like', "%{$search}%")
                ->orWhere('contact_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('rfc', 'like', "%{$search}%");
        });
    }
}
