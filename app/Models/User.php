<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Str;

/**
 * @OA\Schema(
 *     schema="User",
 *     required={"id", "name", "email", "role"},
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="password", type="string", format="password"),
 *     @OA\Property(property="role", type="string")
 * )
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    // Disable auto-incrementing because we use UUIDs
    public $incrementing = false;

    // The primary key type is string (UUID)
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    // Automatically generate UUID when creating a new user
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // JWTSubject interface methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
