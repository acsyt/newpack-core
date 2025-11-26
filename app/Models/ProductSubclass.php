<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSubclass extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = ['product_class_id', 'name', 'description'];

    public function productClass()
    {
        return $this->belongsTo(ProductClass::class);
    }

    public function getActivityLogOptions(): LogOptions {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('ProductSubclass')
            ->dontSubmitEmptyLogs()
            ->logOnlyDirty();
    }
}
