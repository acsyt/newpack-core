<?php

namespace App\Models;

use App\StateMachines\TransferStatusStateMachine;
use Asantibanez\LaravelEloquentStateMachines\Traits\HasStateMachines;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Transfer extends Model
{
    use HasFactory, HasStateMachines;

    protected $fillable = [
        'transfer_number',
        'source_warehouse_id',
        'destination_warehouse_id',
        'status',
        'shipped_at',
        'received_at',
        'shipped_by_user_id',
        'received_by_user_id',
        'notes',
        'receiving_notes',
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
        'received_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $stateMachines = [
        'status' => TransferStatusStateMachine::class
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transfer) {
            if (empty($transfer->transfer_number)) {
                $transfer->transfer_number = self::generateTransferNumber();
            }
        });
    }

    public static function generateTransferNumber(): string
    {
        $date = now()->format('Ymd');
        $lastTransfer = self::whereDate('created_at', now()->toDateString())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastTransfer ? (int) Str::substr($lastTransfer->transfer_number, -4) + 1 : 1;

        return sprintf('TRF-%s-%04d', $date, $sequence);
    }

    // Relationships
    public function sourceWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'source_warehouse_id');
    }

    public function destinationWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'destination_warehouse_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransferItem::class);
    }

    public function shippedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shipped_by_user_id');
    }

    public function receivedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by_user_id');
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        if (is_array($status)) {
            return $query->whereIn('status', $status);
        }
        return $query->where('status', $status);
    }

    public function scopeBySourceWarehouse($query, $warehouseId)
    {
        return $query->where('source_warehouse_id', $warehouseId);
    }

    public function scopeByDestinationWarehouse($query, $warehouseId)
    {
        return $query->where('destination_warehouse_id', $warehouseId);
    }

    public function scopeSearch($query, ?string $search)
    {
        if (!$search) return $query;

        return $query->where('transfer_number', 'like', "%{$search}%");
    }
}
