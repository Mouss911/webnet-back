<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Coupon",
 *     title="Coupon",
 *     description="Modèle de coupon de réduction",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="code", type="string", example="SUMMER2025"),
 *     @OA\Property(property="type", type="string", enum={"fixed", "percentage"}, example="percentage"),
 *     @OA\Property(property="value", type="number", format="float", example=10),
 *     @OA\Property(property="min_purchase", type="number", format="float", example=100),
 *     @OA\Property(property="expires_at", type="string", format="date-time"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_purchase',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];

    /**
     * Check if coupon is valid
     */
    public function isValid(): bool
    {
        return !$this->expires_at->isPast();
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount(float $amount): float
    {
        if ($this->type === 'percentage') {
            return ($amount * $this->value) / 100;
        }
        return $this->value;
    }
}
