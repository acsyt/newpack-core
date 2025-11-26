<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductType extends Model
{
    use HasFactory;

    const PRODUCT_TYPE_FINISHED = 'PT'; // Producto terminado
    const PRODUCT_TYPE_RAW = 'MP'; // Materia prima
    const PRODUCT_TYPE_SERVICE = 'SERV'; // Servicio

    const PRODUCT_TYPE_REFACEMENT = 'REF'; // Refacciones


    protected $fillable = [
        'name',
        'slug',
    ];
}
