<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="API Endpoints pour l'authentification"
 * )
 */
class ApiController extends Controller
{
    /**
     * @OA\Post(
     *     path="/register",
     *     tags={"Authentication"},
     *     summary="Inscription d'un nouvel utilisateur",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Utilisateur créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", ref="#/components/schemas/User"),
     *             @OA\Property(property="token", type="string", example="1|LsKPsdpsY...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Erreur de validation"
     *     )
     * )
     */
    public function register(Request $request): JsonResponse
    {
         $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|regex:/^\+?[1-9]\d{1,14}$/' // Format E.164
         ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            $response = [
                'status' => false,
                'message' => $errorMessage,
            ];
            return response()->json($response, 401);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user' // Définit automatiquement comme utilisateur normal
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/login",
     *     tags={"Authentication"},
     *     summary="Connexion utilisateur",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Connexion réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="token", type="string", example="1|LsKPsdpsY...")
     *         )
     *     )
     * )
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            $response = [
                'status' => false,
                'message' => $errorMessage,
            ];
            return response()->json($response, 401);
        }

        // Verifier le user par email
        $user = User::where('email', $request->email)->first();

        // Vérifier le mot de passe
        if (!empty($user)){
            if (Hash::check($request->password, $user->password)) {
                // Login is OK
                $tokenInfo = $user->createToken('cairocoders-ednalan');

                $token = $tokenInfo->plainTextToken; // Token value

                return response()->json([
                    'status' => true,
                    'message' => 'Login successful',
                    'token' => $token
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Password is incorrect'
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials'
            ]);
        }
    }

    /**
     * @OA\Get(
     *     path="/profile",
     *     tags={"Authentication"},
     *     summary="Affiche le profil de l'utilisateur connecté",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Informations du profil",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Profile information"),
     *             @OA\Property(property="data", ref="#/components/schemas/User")
     *         )
     *     )
     * )
     */
    public function profile(): JsonResponse
    {
        // $user = $request->user();

        $userData = auth()->user();

            return response()->json([
                'status' => true,
                'message' => 'Profile information',
                "data" => $userData
            ]);  
    }

    /**
     * @OA\Get(
     *     path="/logout",
     *     tags={"Authentication"},
     *     summary="Déconnexion de l'utilisateur",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Déconnexion réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User logged out")
     *         )
     *     )
     * )
     */
    public function logout(): JsonResponse
    {
        // $user = $request->user();

        request()->user()->tokens()->delete();

            return response()->json([
                'status' => true,
                'message' => 'User logged out',
            ]);  
    }

    /**
     * @OA\Get(
     *     path="/refresh-token",
     *     tags={"Authentication"},
     *     summary="Rafraîchit le token d'authentification",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token rafraîchi avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Token refreshed successfully"),
     *             @OA\Property(property="access_token", type="string", example="1|newTokenHere...")
     *         )
     *     )
     * )
     */
    public function refreshToken(): JsonResponse
    {
        $tokenInfo = request()->$user()->createToken('newtokencairocoders-ednalan');

        $newToken = $tokenInfo->plainTextToken; // Token value

        return response()->json([
            'status' => true,
            'message' => 'Token refreshed successfully',
            'access_token' => $newToken
        ]);
    }
}
