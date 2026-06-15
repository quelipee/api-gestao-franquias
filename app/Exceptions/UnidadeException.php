<?php

namespace App\Exceptions;

use App\Models\Unidade;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UnidadeException extends Exception
{
    protected int $statusCode;

    public function __construct(string $message, int $statusCode)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }

    public static function UnidadeInvalida(): UnidadeException
    {
        return new self('Unidade não encontrada.',
            ResponseAlias::HTTP_NOT_FOUND);
    }

    public static function UnidadeInativa(Unidade $unidade): UnidadeException
    {
        return new self(
            'Unidade ' . $unidade->nome . ' está inativa.',
            ResponseAlias::HTTP_FORBIDDEN
        );
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], $this->statusCode);
    }
}
