<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PreferencesController extends Controller
{
    /**
     * Default preference structure.
     * This ensures we always return a complete, predictable shape.
     */
    private const DEFAULTS = [
        'theme'   => 'system',  // 'light' | 'dark' | 'system'
        'density' => 'comfortable', // 'compact' | 'comfortable' | 'spacious'
        'privacy' => [
            'save_chat' => true,
            'analytics' => true,
        ],
    ];

    /**
     * GET /api/v1/auth/preferences
     *
     * Returns the authenticated user's stored preferences merged with defaults.
     */
    public function show(Request $request): JsonResponse
    {
        $preferences = array_replace_recursive(
            self::DEFAULTS,
            $request->user()->preferences ?? []
        );

        return response()->json([
            'success' => true,
            'data'    => $preferences,
        ]);
    }

    /**
     * PUT /api/v1/auth/preferences
     *
     * Validates and persists only the supported preference keys.
     * Uses a deep merge so a partial payload is safe.
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'theme'            => ['sometimes', 'string', 'in:light,dark,system'],
            'density'          => ['sometimes', 'string', 'in:compact,comfortable,spacious'],
            'privacy'          => ['sometimes', 'array'],
            'privacy.save_chat'=> ['sometimes', 'boolean'],
            'privacy.analytics'=> ['sometimes', 'boolean'],
        ]);

        $user = $request->user();

        // Deep-merge: existing prefs → defaults → new values
        $current = array_replace_recursive(self::DEFAULTS, $user->preferences ?? []);
        $merged  = array_replace_recursive($current, $validated);

        $user->preferences = $merged;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Preferences updated.',
            'data'    => $merged,
        ]);
    }
}
