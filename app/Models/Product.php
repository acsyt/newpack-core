<?php

namespace App\Models;

use App\Models\ProductType as ProductTypeModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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

    public function productType()
    {
        return $this->belongsTo(ProductTypeModel::class, 'product_type_id');
    }

    public function specs()
    {
        return $this->hasOne(ProductSpec::class);
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
            $q->where('code', ProductType::PRODUCT_TYPE_RAW);
        });
    }

    public function scopeCompound($query)
    {
        return $query->whereHas('productType', function ($q) {
            $q->where('code', ProductType::PRODUCT_TYPE_COMPOUND);
        });
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('sku', 'like', "%{$search}%");
    }

    public function scopeWithSpecs($query)
    {
        return $query->with('specs');
    }
}
