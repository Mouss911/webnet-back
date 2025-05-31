<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Cart",
 *     description="API Endpoints pour la gestion du panier"
 * )
 */
class CartController extends Controller
{
    /**
     * @OA\Get(
     *     path="/cart",
     *     tags={"Cart"},
     *     summary="Affiche le panier de l'utilisateur",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Panier récupéré avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Cart")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     )
     * )
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
     * @OA\Post(
     *     path="/cart/add",
     *     tags={"Cart"},
     *     summary="Ajoute un produit au panier",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id","quantity"},
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="quantity", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Produit ajouté au panier",
     *         @OA\JsonContent(ref="#/components/schemas/CartItem")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides"
     *     )
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/cart/update/{cartItem}",
     *     tags={"Cart"},
     *     summary="Met à jour la quantité d'un produit dans le panier",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="cartItem",
     *         in="path",
     *         required=true,
     *         description="ID de l'item du panier",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"quantity"},
     *             @OA\Property(property="quantity", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item du panier mis à jour",
     *         @OA\JsonContent(ref="#/components/schemas/CartItem")
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/cart/remove/{cartItem}",
     *     tags={"Cart"},
     *     summary="Supprime un produit du panier",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="cartItem",
     *         in="path",
     *         required=true,
     *         description="ID de l'item du panier",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Item supprimé avec succès"
     *     )
     * )
     */
    public function removeItem(CartItem $cartItem): JsonResponse
    {
        if ($cartItem->cart->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $cartItem->delete();
        return response()->json(null, 204);
    }
}
