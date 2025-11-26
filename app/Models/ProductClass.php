<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProductClass extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = ['name', 'code', 'description', 'slug'];

    public function getActivityLogOptions(): LogOptions {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('ProductClass')
            ->dontSubmitEmptyLogs()
            ->logOnlyDirty();
    }
    public function subclasses()
    {
        return $this->hasMany(ProductSubclass::class);
    }
}
