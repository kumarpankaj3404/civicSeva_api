<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * GET /api/v1/auth/me
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                => $user->id,
                'name'              => $user->name,
                'email'             => $user->email,
                'phone'             => $user->phone,
                'avatar'            => $user->avatar,
                'state'             => $user->state,
                'district'          => $user->district,
                'date_of_birth'     => $user->date_of_birth?->toDateString(),
                'gender'            => $user->gender,
                'annual_income'     => $user->annual_income,
                'caste_category'    => $user->caste_category,
                'profile_completed' => $user->profile_completed,
                'roles'             => $user->getRoleNames(),
                'preferences'       => array_replace_recursive(
                    ['theme' => 'system', 'density' => 'comfortable', 'privacy' => ['save_chat' => true, 'analytics' => true]],
                    $user->preferences ?? []
                ),
                'created_at'        => $user->created_at,
                'updated_at'        => $user->updated_at,
            ],
        ]);
    }

    /**
     * PUT /api/v1/auth/profile
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'           => ['sometimes', 'string', 'max:255'],
            'phone'          => ['sometimes', 'nullable', 'string', 'max:15'],
            'avatar'         => ['sometimes', 'nullable', 'url'],
            'state'          => ['sometimes', 'nullable', 'string', 'max:100'],
            'district'       => ['sometimes', 'nullable', 'string', 'max:100'],
            'date_of_birth'  => ['sometimes', 'nullable', 'date', 'before:today'],
            'gender'         => ['sometimes', 'nullable', 'in:male,female,other,prefer_not_to_say'],
            'annual_income'  => ['sometimes', 'nullable', 'integer', 'min:0'],
            'caste_category' => ['sometimes', 'nullable', 'in:general,obc,sc,st,ews'],
            'password'       => ['sometimes', 'confirmed', Password::min(8)],
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        // Check profile completeness
        $user->fill($validated);
        $user->profile_completed = $this->isProfileComplete($user);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data'    => [
                'profile_completed' => $user->profile_completed,
            ],
        ]);
    }

    private function isProfileComplete($user): bool
    {
        return filled($user->name)
            && filled($user->phone)
            && filled($user->state)
            && filled($user->district)
            && filled($user->date_of_birth)
            && filled($user->gender);
    }
}
