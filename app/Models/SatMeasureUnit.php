<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


class SatMeasureUnit extends Model {

    use HasFactory, LogsActivity;

    protected $table = 'measure_units';

    protected $fillable = ['name','code', 'description', 'active', 'created_at', 'updated_at','order'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['full_name'];

    public function getFullNameAttribute() {
        return "{$this->code} - {$this->name}";
    }

    public function getActivityLogOptions(): LogOptions {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('SatMeasureUnit')
            ->dontSubmitEmptyLogs()
            ->logOnlyDirty();
    }
}
