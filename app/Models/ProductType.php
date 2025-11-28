<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductType extends Model
{
    use HasFactory;

    const PRODUCT_TYPE_FINISHED = 'PT'; // Producto Terminado
    const PRODUCT_TYPE_RAW = 'MP'; // Materia Prima
    const PRODUCT_TYPE_SERVICE = 'SERV'; // Servicio
    const PRODUCT_TYPE_COMPOUND = 'COMP'; // Compuesto
    const PRODUCT_TYPE_REFACEMENT = 'REF'; // Refacciones

    protected $fillable = [
        'name',
        'code',
        'slug',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
