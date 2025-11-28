<?php

namespace App\Models;

use App\Enums\ProductType;
use App\Models\ProductType as ProductTypeModel;
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
        'slug',
        'sku',
        'product_type_id',
        'measure_unit_id',
        'product_class_id',
        'product_subclass_id',
        'average_cost',
        'last_purchase_price',
        'current_stock',
        'min_stock',
        'max_stock',
        'is_active',
        'is_sellable',
        'is_purchasable',
        'width',
        'width_min',
        'width_max',
        'gusset',
        'gusset_min',
        'gusset_max',
        'length',
        'length_min',
        'length_max',
        'gauge',
        'gauge_min',
        'gauge_max',
        'nominal_weight',
        'weight_min',
        'weight_max',
        'resin_type',
        'color',
        'additive',
    ];

    protected $casts = [
        'average_cost' => 'decimal:4',
        'current_stock' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    public function getActivityLogOptions(): LogOptions {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('Product')
            ->dontSubmitEmptyLogs()
            ->logOnlyDirty();
    }

    // RELACIONES

    public function productType()
    {
        return $this->belongsTo(ProductTypeModel::class, 'product_type_id');
    }

    public function measureUnit()
    {
        return $this->belongsTo(MeasureUnit::class, 'measure_unit_id');
    }

    public function productClass()
    {
        return $this->belongsTo(ProductClass::class);
    }

    public function productSubclass()
    {
        return $this->belongsTo(ProductSubclass::class);
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_product')
            ->withTimestamps();
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
        return $query->whereHas('productType', function ($q) {
            $q->where('code', ProductType::RAW_MATERIAL->value);
        });
    }

    public function scopeCompound($query)
    {
        return $query->whereHas('productType', function ($q) {
            $q->where('code', ProductType::COMPOUND->value);
        });
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('sku', 'like', "%{$search}%");
    }
}
