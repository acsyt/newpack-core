<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'product_id',
        'op_number',
        'quantity_planned',
        'quantity_produced',
        'status',
        'priority',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'quantity_planned' => 'decimal:4',
        'quantity_produced' => 'decimal:4',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }
}
