<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserAlreadyExistsException extends Exception
{
    public static function EmailAlreadyExists(): self
    {
        return new self("User with this email already exists.");
    }

    public static function InvalidPassword(): self
    {
        return new self("Invalid password.");
    }

    public function render() : JsonResponse
    {
        return response()->json([
            'message' => 'Credenciais inválidas.',
        ], ResponseAlias::HTTP_UNAUTHORIZED);
    }
}
