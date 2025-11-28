<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProductSpec extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'product_id',
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
        'width' => 'decimal:4',
        'width_min' => 'decimal:4',
        'width_max' => 'decimal:4',
        'gusset' => 'decimal:4',
        'gusset_min' => 'decimal:4',
        'gusset_max' => 'decimal:4',
        'length' => 'decimal:4',
        'length_min' => 'decimal:4',
        'length_max' => 'decimal:4',
        'gauge' => 'decimal:4',
        'gauge_min' => 'decimal:4',
        'gauge_max' => 'decimal:4',
        'nominal_weight' => 'decimal:6',
        'weight_min' => 'decimal:6',
        'weight_max' => 'decimal:6',
    ];

    public function getActivityLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('ProductSpec')
            ->dontSubmitEmptyLogs()
            ->logOnlyDirty();
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

