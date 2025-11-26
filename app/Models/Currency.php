<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Currency extends Model
{
    use HasFactory, LogsActivity;


    protected $fillable = [
        'name',
        'code',
        'active',
    ];

    protected $casts = [
        'active'=> 'boolean',
    ];

    public function getActivityLogOptions(): LogOptions {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('Currency')
            ->dontSubmitEmptyLogs()
            ->logOnlyDirty();
    }
}
