<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     schema="Order",
 *     title="Order",
 *     description="Modèle de commande",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="total_amount", type="number", format="float", example=1299.99),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(property="shipping_address", type="string", example="123 Rue Example"),
 *     @OA\Property(property="billing_address", type="string", example="123 Rue Example"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
        'shipping_address',
        'billing_address',
        'payment_status'
    ];

    /**
     * Get the user that owns the order
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items for the order
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Calculate total amount of the order
     */
    public function calculateTotal(): float
    {
        return $this->orderItems->sum(function($item) {
            return $item->price * $item->quantity;
        });
    }
}
