<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Admin",
 *     description="API Endpoints pour l'administration"
 * )
 */
class AdminController extends Controller
{
    /**
     * @OA\Get(
     *     path="/admin/dashboard/stats",
     *     tags={"Admin"},
     *     summary="Obtient les statistiques du tableau de bord",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Statistiques récupérées avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="total_users", type="integer", example=100),
     *             @OA\Property(property="total_orders", type="integer", example=500),
     *             @OA\Property(property="total_products", type="integer", example=200),
     *             @OA\Property(property="revenue", type="number", format="float", example=15000.50)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès non autorisé"
     *     )
     * )
     */
    public function getDashboardStats(): JsonResponse
    {
        $stats = [
            'total_users' => User::count(),
            'total_orders' => Order::count(),
            'total_products' => Product::count(),
            'revenue' => Order::where('status', 'completed')->sum('total_amount')
        ];

        return response()->json($stats);
    }

    /**
     * @OA\Get(
     *     path="/admin/orders/stats",
     *     tags={"Admin"},
     *     summary="Obtient les statistiques des commandes",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Statistiques des commandes récupérées avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="pending", type="integer", example=10),
     *             @OA\Property(property="processing", type="integer", example=5),
     *             @OA\Property(property="completed", type="integer", example=85),
     *             @OA\Property(property="cancelled", type="integer", example=15)
     *         )
     *     )
     * )
     */
    public function getOrderStats(): JsonResponse
    {
        $orderStats = [
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count()
        ];

        return response()->json($orderStats);
    }

    /**
     * @OA\Get(
     *     path="/admin/users",
     *     tags={"Admin"},
     *     summary="Liste tous les utilisateurs",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des utilisateurs récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/User")
     *             ),
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="last_page", type="integer"),
     *             @OA\Property(property="per_page", type="integer"),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     )
     * )
     */
    public function getAllUsers(): JsonResponse
    {
        $users = User::paginate(10);
        return response()->json($users);
    }

    /**
     * @OA\Put(
     *     path="/admin/users/{user}/role",
     *     tags={"Admin"},
     *     summary="Met à jour le rôle d'un utilisateur",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="ID de l'utilisateur",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"role"},
     *             @OA\Property(property="role", type="string", enum={"user", "admin"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rôle mis à jour avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès non autorisé"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Utilisateur non trouvé"
     *     )
     * )
     */
    public function updateUserRole(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'role' => 'required|in:user,admin'
        ]);

        $user->update(['role' => $validated['role']]);
        return response()->json($user);
    }
}
