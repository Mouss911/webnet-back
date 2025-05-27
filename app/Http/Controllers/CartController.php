<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $cart = Cart::where('user_id', auth()->id())
            ->where('status', 'active')
            ->with('cartItems.product')
            ->firstOrCreate(['user_id' => auth()->id()]);

        return response()->json($cart);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function addItem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::where('user_id', auth()->id())
            ->where('status', 'active')
            ->firstOrCreate(['user_id' => auth()->id()]);

        $cartItem = $cart->cartItems()->updateOrCreate(
            ['product_id' => $validated['product_id']],
            [
                'quantity' => $validated['quantity'],
                'price' => $validated['quantity'] * $request->price
            ]
        );

        return response()->json($cartItem->load('product'), 201);
    }

    public function updateItem(Request $request, CartItem $cartItem): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        if ($cartItem->cart->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $cartItem->update([
            'quantity' => $validated['quantity'],
            'price' => $validated['quantity'] * $cartItem->product->price
        ]);

        return response()->json($cartItem->load('product'));
    }

    public function removeItem(CartItem $cartItem): JsonResponse
    {
        if ($cartItem->cart->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $cartItem->delete();
        return response()->json(null, 204);
    }
}
