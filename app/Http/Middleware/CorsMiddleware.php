<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // CORS config
        $allowedOrigins = config('cors.allowed_origins', ['*']);
        $allowedMethods = config('cors.allowed_methods', ['*']);
        $allowedHeaders = config('cors.allowed_headers', ['Content-Type', 'Authorization', 'X-CSRF-TOKEN']);
        $maxAge = config('cors.max_age', 86400);
        $supportsCredentials = config('cors.supports_credentials', false);

        $origin = $request->headers->get('Origin');
        $isOriginAllowed = in_array($origin, $allowedOrigins) || in_array('*', $allowedOrigins);

        $allowOrigin = $isOriginAllowed ? ($origin ?: '*') : '*';

        // Handle preflight request
        if ($request->getMethod() === 'OPTIONS') {
            $response = response()->noContent(200);
        } else {
            $response = $next($request);
        }

        // Set CORS headers
        $response->headers->set('Access-Control-Allow-Origin', $allowOrigin);
        $response->headers->set('Access-Control-Allow-Methods', implode(', ', $allowedMethods));
        $response->headers->set('Access-Control-Allow-Headers', implode(', ', $allowedHeaders));
        $response->headers->set('Access-Control-Max-Age', (string) $maxAge);

        if ($supportsCredentials) {
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        }

        return $response;
    }
}
