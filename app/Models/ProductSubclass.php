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

    protected $fillable = ['product_class_id', 'name', 'code', 'description', 'slug'];

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

    public function scopeSearch($query, ?string $search)
    {
        if (!$search) return $query;

        return $query->where(function ($q) use ($search) {
            $q->where('code', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }
}
