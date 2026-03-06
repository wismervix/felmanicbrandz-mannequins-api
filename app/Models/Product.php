<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Allow mass assignment for these fields
    protected $fillable = [
        'title',
        'description',
        'category',
        'price',
        'discount_percentage',
        'rating',
        'stock',
        'brand',
        'sku',
        'weight',
        'warranty_information',
        'shipping_information',
        'availability_status',
        'return_policy',
        'minimum_order_quantity',
        'tags',
        'images',
        'dimensions',
        'reviews',
        'meta',
        'thumbnail',
    ];

    // Casts for numeric fields (optional but useful)
    protected $casts = [
        'tags' => 'array',
        'images' => 'array',
        'dimensions' => 'array',
        'reviews' => 'array',
        'meta' => 'array',
        'price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'rating' => 'decimal:2',
        'weight' => 'integer',
        'stock' => 'integer',
        'minimum_order_quantity' => 'integer',
    ];
}
