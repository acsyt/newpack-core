<?php



namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


class RoleHasPermission extends Model {

    use HasFactory;

    protected $table = 'role_has_permissions';

    public $timestamps = false;


    protected $fillable = [
        'role_id',
        'permission_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getActivityLogOptions(): LogOptions {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('RoleHasPermission')
            ->dontSubmitEmptyLogs()
            ->logOnlyDirty();
    }
}
