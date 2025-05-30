<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
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

    public function getAllUsers(): JsonResponse
    {
        $users = User::paginate(10);
        return response()->json($users);
    }

    public function updateUserRole(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'role' => 'required|in:user,admin'
        ]);

        $user->update(['role' => $validated['role']]);
        return response()->json($user);
    }
}
