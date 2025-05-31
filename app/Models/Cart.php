<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     schema="Cart",
 *     title="Cart",
 *     description="Modèle de panier",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="status", type="string", enum={"active", "completed"}, example="active"),
 *     @OA\Property(
 *         property="cart_items",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/CartItem")
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'status'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Calculate total amount of cart
     */
    public function getTotal(): float
    {
        return $this->cartItems->sum(function($item) {
            return $item->price * $item->quantity;
        });
    }
}
