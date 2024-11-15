<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckCustomHeader
{
    public function handle(Request $request, Closure $next)
    {
        $expectedHeaderValue = env('X-API-KEY');
        $headerValue = $request->header('X-Custom-Header');

        if ($headerValue !== $expectedHeaderValue) {
            return response()->json([
                'code' => 403,
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }

        return $next($request);
    }
}
