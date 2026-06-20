<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UnidadeProdutoException extends Exception
{
    protected int $statusCode;

    public function __construct(string $message, int $statusCode)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }

    public static function ProdutoNaoVinculado(): self
    {
        return new self(
            'Produto não está vinculado à unidade.',
            ResponseAlias::HTTP_NOT_FOUND
        );
    }

    public static function NaoExisteProduto(): self
    {
        return new self(
            'Não encontramos produtos nesta unidade.',
            ResponseAlias::HTTP_NOT_FOUND
        );
    }

    public static function ProdutoJaVinculado(): self
    {
        return new self(
          'Produto já foi cadastrado.',
          ResponseAlias::HTTP_NOT_FOUND
        );
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], $this->statusCode);
    }
}
