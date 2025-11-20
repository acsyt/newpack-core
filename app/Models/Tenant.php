<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasFactory, HasDatabase, HasDomains, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'code',
        'active',
        'uuid',
    ];

    protected $hidden = [
        'data'
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'uuid',
            'name',
            'code',
            'status',
        ];
    }

    public static function boot() {
        parent::boot();
        static::creating(fn($domain) => $domain->uuid = (string) Str::uuid());
    }


    public function getIncrementing() {
        return true;
    }



    public function getActivityLogOptions(): LogOptions {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('Tenant')
            ->dontSubmitEmptyLogs()
            ->logOnlyDirty();
    }

}
