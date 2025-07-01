<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Product",
 *     title="Product",
 *     description="A product that is sold by a shop",
 *     required={"shop_id", "name", "price", "stock"},
 *     @OA\Property(property="id", type="integer", example=1, description="Unique identifier of the product"),
 *     @OA\Property(property="shop_id", type="integer", example=1, description="ID of the shop that owns the product"),
 *     @OA\Property(property="name", type="string", example="Black Distro T-Shirt", description="Name of the product"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Made from 30s cotton combed fabric, very comfortable to wear.", description="Product description"),
 *     @OA\Property(property="price", type="integer", example=95000, description="Price of the product in IDR"),
 *     @OA\Property(property="stock", type="integer", example=20, description="Number of items available in stock"),
 *     @OA\Property(property="image_url", type="string", nullable=true, example="https://example.com/images/tshirt.jpg", description="URL to the product image"),
 *     @OA\Property(property="is_active", type="boolean", example=true, description="Product status (true if available)"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-01T08:00:00Z", description="Date and time when the product was created")
 * )
 */
class Product extends Model
{
    protected $fillable = [
        'shop_id',
        'name',
        'description',
        'price',
        'stock',
        'image_url',
        'is_active',
    ];

    public $timestamps = false;

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
