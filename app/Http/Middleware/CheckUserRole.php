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

        $user = $request->user();
        $userRole = $user->role instanceof UserRole
            ? $user->role->value
            : $user->role;

        if (!in_array($userRole, $roles)) {
            return response()->json([
                'message' => 'Acesso negado. Esta ação é permitida apenas para administradores ou gerentes.'
            ], Response::HTTP_FORBIDDEN);
        }

        if ($userRole == UserRole::GERENTE->value) {
            $unidadeId = $request->route('unidade_id') ?? $request->unidade_id;

            if (!$unidadeId) return response()->json([
                'message' => 'Unidade não encontrada.'
            ],Response::HTTP_BAD_REQUEST);

            $acesso = $user->unidades()->where('unidade_id', $unidadeId)->exists();

            if (!$acesso) return response()->json([
                'message' => 'Você não tem permissão a está unidade.'
            ], Response::HTTP_FORBIDDEN);
        }
        return $next($request);
    }
}
