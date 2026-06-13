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
        $validated = $request->validated();
        return new self(
            nome: $validated['nome'],
            descricao: $validated['descricao'],
            preco: (float)$validated['preco'],
        );
    }
}
