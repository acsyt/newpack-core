<?php

namespace App\Models;

use App\Enums\ProductType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// Asegúrate de tener instalado: composer require spatie/laravel-activitylog
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'sku',
        'type',
        'measure_unit_id',
        'product_class_id',
        'product_subclass_id',
        'average_cost',
        'last_purchase_price',
        'current_stock',
        'min_stock',
        'max_stock',
        'track_batches',
        'is_active',
        'is_sellable',
        'is_purchasable',
    ];

    protected $casts = [
        'type' => ProductType::class,
        'average_cost' => 'decimal:4',
        'current_stock' => 'decimal:4',
        'is_active' => 'boolean',
        'track_batches' => 'boolean',
    ];

    public function getActivityLogOptions(): LogOptions {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('Product')
            ->dontSubmitEmptyLogs()
            ->logOnlyDirty();
    }

    // RELACIONES

    public function measureUnit()
    {
        return $this->belongsTo(SatMeasureUnit::class, 'measure_unit_id');
    }

    public function productClass()
    {
        return $this->belongsTo(ProductClass::class);
    }

    public function productSubclass()
    {
        return $this->belongsTo(ProductSubclass::class);
    }

    public function ingredients()
    {
        return $this->belongsToMany(Product::class, 'product_compounds', 'compound_id', 'ingredient_id')
            ->using(ProductCompound::class)
            ->withPivot(['id', 'quantity', 'wastage_percent', 'process_stage', 'is_active'])
            ->withTimestamps();
    }

    public function usedInCompounds()
    {
        return $this->belongsToMany(Product::class, 'product_compounds', 'ingredient_id', 'compound_id')
            ->using(ProductCompound::class)
            ->withPivot(['id', 'quantity', 'wastage_percent', 'process_stage', 'is_active'])
            ->withTimestamps();
    }

    public function scopeRawMaterial($query)
    {
        return $query->where('type', ProductType::RAW_MATERIAL);
    }

    public function scopeCompound($query)
    {
        return $query->where('type', ProductType::COMPOUND);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('sku', 'like', "%{$search}%");
    }
}
