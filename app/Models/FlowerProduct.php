<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowerProduct extends Model
{
    use HasFactory;

    protected $table = 'flower_products';

    protected $fillable = [
        'product_id',
        'name',
        'slug',
        'price',
        'mrp',
        'description',
        'category',
        'pooja_id',
        'stock',
        'duration',
        'product_image',
        
    ];
    
}


