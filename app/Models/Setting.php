<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Setting extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'slug',
        'value',
        'description',
    ];

    protected const EXCHANGE_RATE = 'exchange_rate';

    protected const IVA = 'iva';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getActivityLogOptions(): LogOptions {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('Setting')
            ->dontSubmitEmptyLogs()
            ->logOnlyDirty();
    }

    public static function iva() {
        return (float) self::where('slug', self::IVA)->first()->value;
    }

    public static function exchange_rate() {
        return (float) self::where('slug', self::EXCHANGE_RATE)->first()->value;
    }

}
