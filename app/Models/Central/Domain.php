<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Models\Domain as BaseDomain;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


class Domain extends BaseDomain {

    use HasFactory, LogsActivity;

    protected $fillable = [
        'domain',
        'tenant_id',
        'uuid',
        'domain_configured',
        'domain_created',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function boot() {
        parent::boot();
        static::creating(fn($domain) => $domain->uuid = (string) Str::uuid());
    }


    public function getActivityLogOptions(): LogOptions {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('Domain')
            ->dontSubmitEmptyLogs()
            ->logOnlyDirty();
    }

    public function tenant() {
        return $this->belongsTo(\App\Models\Tenant::class);
    }

}
