<?php

namespace App\DTOs\UnidadeProduto;

use App\Http\Requests\UnidadeProdutoRequest;

readonly class UnidadeProdutoDTO
{
    public function __construct(
        public int  $produto_id,
        public int  $unidade_id,
        public bool $disponivel,
    )
    {
    }

    public static function fromRequest(UnidadeProdutoRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            produto_id: $validated['produto_id'],
            unidade_id: $validated['unidade_id'],
            disponivel: $validated['disponivel'],
        );
    }

    public function toArray(): array
    {
        return [
            'produto_id' => $this->produto_id,
            'unidade_id' => $this->unidade_id,
            'disponivel' => $this->disponivel,
        ];
    }
}
