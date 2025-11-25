<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use HasFactory, LogsActivity;

    public function getActivityLogOptions(): LogOptions {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('Product')
            ->dontSubmitEmptyLogs()
            ->logOnlyDirty();
    }
}
