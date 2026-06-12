<?php

namespace App\DTOs\Produto;

use App\Http\Requests\ProdutoStoreRequest;

readonly class ProdutoDataDTO
{
    public function __construct(
        public string $nome,
        public string $descricao,
        public string $preco,
    )
    {
    }

    public static function fromRequest(ProdutoStoreRequest $request): self
    {
        return new self(
            nome: $request->validated('nome'),
            descricao: $request->validated('descricao'),
            preco: (float)$request->validated('preco'),
        );
    }
}
