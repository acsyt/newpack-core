<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class WarehouseLocation extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'warehouse_id',
        'aisle',
        'shelf',
        'section',
    ];

    protected $casts = [
        'warehouse_id'  => 'integer',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];


    public function getActivityLogOptions(): LogOptions {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('WarehouseLocation')
            ->dontSubmitEmptyLogs()
            ->logOnlyDirty();
    }

    public function warehouse() {
        return $this->belongsTo(Warehouse::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('aisle', 'like', "%{$search}%")
            ->orWhere('shelf', 'like', "%{$search}%")
            ->orWhere('section', 'like', "%{$search}%")
            ->orWhere('unique_id', 'like', "%{$search}%");
    }
}
