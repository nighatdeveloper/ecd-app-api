<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GoogleLoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\GoogleTokenVerifier;
use Illuminate\Http\Request;

class GoogleAuthController extends Controller
{
    public function __construct(
        private GoogleTokenVerifier $googleTokenVerifier
    ) {}

    /**
     * POST /api/v1/auth/google-login
     */
    public function login(GoogleLoginRequest $request)
    {
        $data = $request->validated();

        // Verify Google Access Token
        $googlePayload = $this->googleTokenVerifier->verify(
            $data['google_access_token']
        );

        if (!$googlePayload) {
            return response()->json([
                'success' => false,
                'message' => 'The provided Google access token is invalid or has expired.',
            ], 401);
        }

        // Check email from Google token
        if (
            empty($googlePayload['email']) ||
            strtolower($googlePayload['email']) !== strtolower($data['email'])
        ) {
            return response()->json([
                'success' => false,
                'message' => 'The Google access token does not match the provided email.',
            ], 422);
        }

        // Find existing user
        $user = User::where('email', $data['email'])->first();

        $isNewUser = !$user;

        // Create new user
        if ($isNewUser) {
            $user = User::create([
                'email' => $data['email'],
                'name' => $data['name'],
                'profile_image' => $data['profile_image'] ?? null,
            ]);
        } else {
            // Update existing user's information
            $user->update([
                'name' => $data['name'],
                'profile_image' => $data['profile_image'] ?? $user->profile_image,
            ]);
        }

        // Create Laravel authentication token
        $token = $user->createToken('auth')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => $isNewUser
                ? 'New user account created successfully.'
                : 'User already exists. Login successful.',
            'user_type' => $isNewUser ? 'new' : 'existing',
            'token' => $token,
            'user' => new UserResource($user->fresh()),
        ], $isNewUser ? 201 : 200);
    }

    /**
     * POST /api/v1/auth/logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }
}

