<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


class SatCode extends Model {

    use HasFactory, LogsActivity;

    protected $fillable = [
        'id',
        'name',
        'code',
        'description',
        'active',
        'include_transfer_vat', //INCLUIR IVA  TRASALADO
        'include_transfer_ieps',//INCLUIR IEPS  TRASALADO
        'border_strip_incentive', //INCENTIVO DE FRANJA FRONTERIZA
        'created_at',
        'update_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['full_name'];

    public function getActivityLogOptions(): LogOptions {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('SatCode')
            ->dontSubmitEmptyLogs()
            ->logOnlyDirty();
    }

    public function getFullNameAttribute() {
        return "{$this->code} - {$this->name}";
    }
}
