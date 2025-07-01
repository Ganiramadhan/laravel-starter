<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class Authenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                $token = $request->cookie('jwt_token');
                if ($token) {
                    JWTAuth::setToken($token);
                }
            }

            if (!$token) {
                // \Log::warning('No token provided');
                return $this->unauthorizedResponse($request, 'Token not provided');
            }

            // \Log::info('Token used: ' . $token);
            $user = JWTAuth::authenticate();
            if (!$user) {
                // \Log::warning('User not found for token');
                return $this->unauthorizedResponse($request, 'User not found');
            }
        } catch (JWTException $e) {
            // \Log::error('JWT Exception: ' . $e->getMessage());
            return $this->unauthorizedResponse($request, $e->getMessage());
        }

        return $next($request);
    }

    private function unauthorizedResponse(Request $request, string $message = 'Unauthorized'): Response
    {
        if (!$request->expectsJson() && !$request->isXmlHttpRequest()) {
            // \Log::info('Redirecting to login');
            return redirect()->route('login');
        }
        return response()->json(['error' => $message], 401);
    }
}
