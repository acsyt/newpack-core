<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// Asegúrate de tener instalado: composer require spatie/laravel-activitylog
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use HasFactory, LogsActivity;
    // Opcional: use SoftDeletes; // Si decides usar borrado lógico

    protected $fillable = [
        'name',
        'sku',
        'type', // 'materia_prima', 'compuesto', 'insumo', 'servicio', 'wip'
        'unit_of_measure',
        'average_cost',
        'last_purchase_price',
        'current_stock',
        'min_stock',
        'max_stock',
        'track_batches',
        'is_active',
        'is_sellable',
        'is_purchasable',
    ];

    protected $casts = [
        'average_cost' => 'decimal:4',
        'current_stock' => 'decimal:4',
        'is_active' => 'boolean',
        'track_batches' => 'boolean',
    ];

    // Configuración de Logs de Actividad (Spatie)
    public function getActivityLogOptions(): LogOptions {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('Product')
            ->dontSubmitEmptyLogs()
            ->logOnlyDirty();
    }

    // RELACIONES

    /**
     * Ingredientes que componen este producto (Si soy un Compuesto).
     * Ejemplo: Bolsa de Basura (Yo) -> requiere -> Polietileno (Ingrediente)
     */
    public function ingredients()
    {
        return $this->belongsToMany(Product::class, 'product_compounds', 'compound_id', 'ingredient_id')
            ->using(ProductCompound::class) // Usar nuestro modelo Pivot personalizado
            ->withPivot(['id', 'quantity', 'wastage_percent', 'process_stage', 'is_active'])
            ->withTimestamps();
    }

    /**
     * Productos donde soy usado como ingrediente (Si soy Materia Prima).
     * Ejemplo: Polietileno (Yo) -> soy usado en -> Bolsa de Basura (Compuesto)
     */
    public function usedInCompounds()
    {
        return $this->belongsToMany(Product::class, 'product_compounds', 'ingredient_id', 'compound_id')
            ->using(ProductCompound::class)
            ->withPivot(['id', 'quantity', 'wastage_percent', 'process_stage', 'is_active'])
            ->withTimestamps();
    }

    // SCOPES (Helpers para consultas limpias)

    public function scopeMateriaPrima($query)
    {
        return $query->where('type', 'materia_prima');
    }

    public function scopeCompuesto($query)
    {
        return $query->where('type', 'compuesto');
    }
}
