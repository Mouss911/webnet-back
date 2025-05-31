<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Coupons",
 *     description="API Endpoints pour la gestion des coupons"
 * )
 */
class CouponController extends Controller
{
    /**
     * @OA\Get(
     *     path="/admin/coupons",
     *     tags={"Coupons"},
     *     summary="Liste tous les coupons",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des coupons récupérée avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Coupon")
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $coupons = Coupon::paginate(10);
        return response()->json($coupons);
    }

    /**
     * @OA\Post(
     *     path="/admin/coupons",
     *     tags={"Coupons"},
     *     summary="Crée un nouveau coupon",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code","type","value","expires_at"},
     *             @OA\Property(property="code", type="string", example="SUMMER2025"),
     *             @OA\Property(property="type", type="string", enum={"fixed","percentage"}, example="percentage"),
     *             @OA\Property(property="value", type="number", example=10),
     *             @OA\Property(property="expires_at", type="string", format="date-time"),
     *             @OA\Property(property="min_purchase", type="number", example=100)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Coupon créé avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Coupon")
     *     )
     * )
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
