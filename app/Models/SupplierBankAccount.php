<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class SupplierBankAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'bank_name',
        'account_number',
        'clabe',
        'swift_code',
        'currency',
        'is_primary',
        'status',
        'notes',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($bankAccount) {
            if ($bankAccount->is_primary) {
                static::where('supplier_id', $bankAccount->supplier_id)
                    ->where('id', '!=', $bankAccount->id)
                    ->update(['is_primary' => false]);
            }
        });
    }
}
