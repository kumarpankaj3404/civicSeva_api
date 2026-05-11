<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * POST /api/v1/auth/login
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Revoke previous tokens of same device_name if sent
        if ($request->has('device_name')) {
            $user->tokens()->where('name', $request->device_name)->delete();
        }

        $token = $user->createToken($request->input('device_name', 'web'))->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data'    => [
                'user'       => $this->formatUser($user),
                'token'      => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * POST /api/v1/auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    private function formatUser(User $user): array
    {
        return [
            'id'                => (string) $user->_id,
            'name'              => $user->name,
            'email'             => $user->email,
            'phone'             => $user->phone,
            'roles'             => $user->getRoleNames(),
            'profile_completed' => $user->profile_completed,
        ];
    }
}
