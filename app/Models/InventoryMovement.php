<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $table = 'inventory_movements';

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'warehouse_location_id',
        'type',
        'quantity',
        'balance_after',
        'batch_id',
        'reference_type',
        'reference_id',
        'user_id',
        'notes',
        'related_movement_id',
        'transfer_id',
    ];

    protected $casts = [
        'type' => \App\Enums\InventoryMovementType::class,
        'quantity' => 'decimal:4',
        'balance_after' => 'decimal:4',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function warehouseLocation(): BelongsTo
    {
        return $this->belongsTo(WarehouseLocation::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function relatedMovement(): BelongsTo
    {
        return $this->belongsTo(InventoryMovement::class, 'related_movement_id');
    }

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(Transfer::class);
    }
}
