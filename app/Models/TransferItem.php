<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferItem extends Model
{
    use HasFactory;

  protected $fillable = [
        'transfer_id',
        'product_id',
        'warehouse_location_source_id',
        'warehouse_location_destination_id',
        'batch_id',
        'quantity_sent',
        'quantity_received',
        'quantity_missing',
        'quantity_damaged',
        'notes',
    ];

    protected $casts = [
        'quantity_sent' => 'decimal:4',
        'quantity_received' => 'decimal:4',
        'quantity_missing' => 'decimal:4',
        'quantity_damaged' => 'decimal:4',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function transfer(): BelongsTo
    {
        return $this->belongsTo(Transfer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function sourceLocation(): BelongsTo
    {
        return $this->belongsTo(WarehouseLocation::class, 'warehouse_location_source_id');
    }

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(WarehouseLocation::class, 'warehouse_location_destination_id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    // Accessors
    public function quantityDiscrepancy(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->quantity_received === null) {
                    return null;
                }
                return $this->quantity_received - $this->quantity_sent;
            }
        );
    }
}
