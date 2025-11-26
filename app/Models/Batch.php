<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Batch extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'product_id',
        'batch_code',
        'production_date',
        'expiration_date',
        'supplier_id',
        'production_order_id',
        'initial_quantity',
        'current_quantity',
        'quality_certificate',
        'notes',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'production_date' => 'date',
        'expiration_date' => 'date',
        'initial_quantity' => 'decimal:4',
        'current_quantity' => 'decimal:4',
        'quality_certificate' => 'array',
    ];

    public function getActivityLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class);
    }

    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // SCOPES

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'active')
            ->where('current_quantity', '>', 0);
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('status', 'active')
            ->whereNotNull('expiration_date')
            ->whereBetween('expiration_date', [now(), now()->addDays($days)]);
    }

    // HELPERS

    public function isAvailable(): bool
    {
        return $this->status === 'active' && $this->current_quantity > 0;
    }

    public function isExpired(): bool
    {
        return $this->expiration_date && now()->greaterThan($this->expiration_date);
    }

    public function deplete(): void
    {
        $this->update([
            'status' => 'depleted',
            'current_quantity' => 0,
        ]);
    }
}
