<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductCompound extends Pivot
{
    protected $table = 'product_compounds';

    public $incrementing = true;

    protected $fillable = [
        'compound_id',
        'ingredient_id',
        'quantity',
        'wastage_percent',
        'process_stage',
        'is_active',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'wastage_percent' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relaciones inversas si alguna vez necesitas acceder desde el pivote hacia arriba
    public function compound()
    {
        return $this->belongsTo(Product::class, 'compound_id');
    }

    public function ingredient()
    {
        return $this->belongsTo(Product::class, 'ingredient_id');
    }
}
