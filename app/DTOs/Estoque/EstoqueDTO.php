<?php

namespace App\DTOs\Estoque;

use App\Http\Requests\EstoqueRequest;

readonly class EstoqueDTO
{
    public function __construct(
        public ?int $unidade_id = null,
        public ?int $produto_id = null,
        public ?int $quantidade = null,
        public ?int $quantidade_minima = null,
    )
    {
    }

    public static function fromRequest(EstoqueRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            unidade_id: $validated['unidade_id'] ?? null,
            produto_id: $validated['produto_id'] ?? null,
            quantidade: $validated['quantidade'] ?? null,
            quantidade_minima: $validated['quantidade_minima'] ?? null,
        );
    }

    public function toArray(): array
    {
        $data = [
            'unidade_id' => $this->unidade_id,
            'produto_id' => $this->produto_id,
            'quantidade' => $this->quantidade,
            'quantidade_minima' => $this->quantidade_minima,
        ];

        return array_filter($data, fn($item) => $item !== null);
    }
}
