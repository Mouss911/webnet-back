<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ApiController extends Controller
{
    // Register
    public function register(Request $request)
    {
        
         $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|min:6|confirmed',
         ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            $response = [
                'status' => false,
                'message' => $errorMessage,
            ];
            return response()->json($response, 401);
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User registered successfully'
        ]);
    }

    // Login
    public function login(Request $request)
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

    // Profile (GET, Auth Token)
    public function profile()
    {
        // $user = $request->user();

        $userData = auth()->user();

            return response()->json([
                'status' => true,
                'message' => 'Profile information',
                "data" => $userData
            ]);  
    }

    // Logout (GET, Auth Token)
    public function logout()
    {
        // $user = $request->user();

        request()->user()->tokens()->delete();

            return response()->json([
                'status' => true,
                'message' => 'User logged out',
            ]);  
    }

    // Refresh Token (GET, Auth Token)
    public function refreshToken()
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
