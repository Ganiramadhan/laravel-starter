<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Shop",
 *     title="Shop",
 *     description="A store owned by a user that sells products.",
 *     required={"user_id", "name", "contact_whatsapp", "subdomain"},
 *     @OA\Property(property="id", type="integer", example=1, description="Unique identifier of the shop"),
 *     @OA\Property(property="user_id", type="string", format="uuid", example="64cac925-f669-4621-9b5d-765af5a96dad", description="UUID of the user who owns the shop"),
 *     @OA\Property(property="name", type="string", example="Toko Fashion A", description="Name of the shop"),
 *     @OA\Property(property="logo_url", type="string", nullable=true, example="https://example.com/logo-a.png", description="URL to the shop logo"),
 *     @OA\Property(property="contact_whatsapp", type="string", example="081234567890", description="WhatsApp contact number of the shop"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Trendy fashion store for young people.", description="Detailed description of the shop"),
 *     @OA\Property(property="subdomain", type="string", example="fashiona", description="Unique subdomain used for the shop's storefront"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-01T08:00:00Z", description="Timestamp when the shop was created")
 * )
 */
class Shop extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'logo_url',
        'contact_whatsapp',
        'description',
        'subdomain'
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
