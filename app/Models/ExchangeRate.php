<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ExchangeRate extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'currency_id',
        'value',
        'active',
        'start_date',
    ];

    protected $casts = [
        'value'         => 'float',
        'active'        => 'boolean',
        'start_date'    => 'date',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    public function currency() {
        return $this->belongsTo(Currency::class);
    }

    public function currentExchangeRate() {
        return $this->where('active', true)->first();
    }

    public function getActivityLogOptions(): LogOptions {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('ExchangeRate')
            ->dontSubmitEmptyLogs()
            ->logOnlyDirty();
    }
}
