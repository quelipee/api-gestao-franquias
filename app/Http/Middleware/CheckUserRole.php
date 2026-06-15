<?php

namespace App\Http\Middleware;

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
        if (!$request->user()) return \response()->json(['message' => 'Não Autorizado.'], Response::HTTP_UNAUTHORIZED);

        $userRole = $request->user()->role instanceof UserRole
            ? $request->user()->role->value
            : $request->user()->role;

        if (!in_array($userRole, $roles)) {
            return response()->json([
                'message' => 'Acesso negado. Esta ação é permitida apenas para administradores ou gerentes.'
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
