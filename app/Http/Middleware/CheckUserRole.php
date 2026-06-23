<?php

namespace App\Http\Middleware;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) return response()->json(['message' => 'Não Autorizado.'], Response::HTTP_UNAUTHORIZED);

        if (!in_array($user->role->value, $roles)) {
            return response()->json([
                'message' => 'Acesso negado para este recurso.'
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
