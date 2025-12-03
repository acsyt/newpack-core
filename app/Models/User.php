<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasApiTokens, LogsActivity;

    protected $fillable = [
        'name',
        'last_name',
        'email',
        'phone',
        'immediate_supervisor_id',
        'password',
        'active',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $guard_name = 'web';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'active'            => 'boolean',
        ];
    }

    public function language(): Attribute {
        return Attribute::make(
            set: fn($value) => Str::lower($value),
            get: fn($value) => Str::lower($value),
        );
    }

    public function email(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Str::lower($value),
            set: fn($value) => Str::lower($value),
        );
    }

    public function getActivityLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('users')
            ->dontSubmitEmptyLogs()
            ->logOnlyDirty();
    }
}
