<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductType extends Model
{
    use HasFactory;

    const PRODUCT_TYPE_FINISHED = 'PT';
    const PRODUCT_TYPE_RAW = 'MP';
    const PRODUCT_TYPE_SERVICE = 'SERV';
    const PRODUCT_TYPE_COMPOUND = 'COMP';
    const PRODUCT_TYPE_REFACEMENT = 'REF';

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
