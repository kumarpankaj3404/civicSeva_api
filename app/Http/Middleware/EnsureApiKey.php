<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiKey
{
    /**
     * Handle an incoming request.
     * Validates the X-AI-Gateway-Key header against AI_GATEWAY_SECRET.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('app.ai_gateway_secret', env('AI_GATEWAY_SECRET'));

        if (! $secret) {
            // If no secret is configured, skip validation (dev mode)
            return $next($request);
        }

        $key = $request->header('X-AI-Gateway-Key');

        if (! $key || ! hash_equals($secret, $key)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or missing API gateway key.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
