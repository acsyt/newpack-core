<?php

namespace App\Models\Shared;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class UserToken extends Model
{
    use HasFactory, LogsActivity, Prunable;

    const TYPE_PASSWORD_RESET = 'password_reset';
    const TYPE_EMAIL_VERIFICATION = 'email_verification';
    const TYPE_2FA_BACKUP = '2fa_backup';

    const TYPE_ACCOUNT_REACTIVATION = 'account_reactivation';

    protected $fillable = [
        'email',
        'token',
        'type',
        'expires_at',
        'resettable_id',
        'resettable_type',
        'metadata',
        'used',
        'used_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'metadata' => 'array',
        'used' => 'boolean',
    ];

    protected $hidden = [
        'token',
    ];

    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $expireInMinutes = match($model->type) {
                self::TYPE_PASSWORD_RESET       => config('auth.passwords.users.expire', 60),
                self::TYPE_EMAIL_VERIFICATION   => config('auth.verification.expire', 60 * 24),
                self::TYPE_2FA_BACKUP           => 30,
                self::TYPE_ACCOUNT_REACTIVATION => 60,
                default                         => 60
            };

            if (!is_numeric($expireInMinutes) || $expireInMinutes <= 0) {
                $expireInMinutes = 60;
            }

            $model->expires_at = Carbon::now()->addMinutes($expireInMinutes);
        });
    }

    public function prunable()
    {
        return static::where(function($query) {
            $query->where('expires_at', '<=', now())
                ->orWhere('used', true);
        });
    }

    public function resettable(): MorphTo
    {
        return $this->morphTo();
    }

    public function markAsUsed(): void
    {
        $this->update([
            'used' => true,
            'used_at' => now(),
        ]);
    }

    // Scopes
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now())
                    ->where('used', false);
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    public function scopeUsed($query)
    {
        return $query->where('used', true);
    }

    public function scopeForEmail($query, string $email)
    {
        return $query->where('email', $email);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopePasswordReset($query)
    {
        return $query->ofType(self::TYPE_PASSWORD_RESET);
    }

    public function scopeEmailVerification($query)
    {
        return $query->ofType(self::TYPE_EMAIL_VERIFICATION);
    }

    public function scopeLatestForEmail($query, string $email)
    {
        return $query->forEmail($email)->orderBy('created_at', 'desc');
    }

    // Métodos de verificación
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->used;
    }

    public function isUsed(): bool
    {
        return $this->used;
    }

    public function getActivityLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('UserToken')
            ->dontSubmitEmptyLogs()
            ->logOnlyDirty();
    }
}
