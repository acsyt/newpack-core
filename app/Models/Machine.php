<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Machine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'process_id',
        'speed_mh',
        'speed_kgh',
        'circumference_total',
        'max_width',
        'max_center',
        'status',
    ];

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }
}
