<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AuthException extends Exception
{
    protected int $statusCode;

    public function __construct(string $message, int $statusCode)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }

    public static function EmailAlreadyExists(): self
    {
        return new self("Este e-mail já está em uso por outro usuário.",
            ResponseAlias::HTTP_CONFLICT);
    }

    public static function InvalidPassword(): self
    {
        return new self("Senha incorreta.",
            ResponseAlias::HTTP_UNAUTHORIZED);
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], $this->statusCode);
    }
}
