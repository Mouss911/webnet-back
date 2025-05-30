<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $coupons = Coupon::paginate(10);
        return response()->json($coupons);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|unique:coupons|string',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric',
            'expires_at' => 'required|date',
            'min_purchase' => 'nullable|numeric'
        ]);

        $coupon = Coupon::create($validated);
        return response()->json($coupon, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Coupon $coupon): JsonResponse
    {
        return response()->json($coupon);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coupon $coupon): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'sometimes|unique:coupons,code,' . $coupon->id,
            'type' => 'sometimes|in:fixed,percentage',
            'value' => 'sometimes|numeric',
            'expires_at' => 'sometimes|date',
            'min_purchase' => 'nullable|numeric'
        ]);

        $coupon->update($validated);
        return response()->json($coupon);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon): JsonResponse
    {
        $coupon->delete();
        return response()->json(null, 204);
    }
}
