<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'batch_id',
        'reference_type',
        'reference_id',
        'location',
        'balance_after',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'balance_after' => 'decimal:4',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
