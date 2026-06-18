<?php

namespace App\DTOs\Produto;

use App\Http\Requests\ProdutoRequest;

readonly class ProdutoDataDTO
{
    public function __construct(
        public ?int $categoria_id,
        public ?string $nome,
        public ?string $descricao,
        public ?float $preco_base,
        public ?bool $disponivel_periodo_junino,
        public ?bool $ativo,
    )
    {
    }

    public static function fromRequest(ProdutoRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            categoria_id: $validated['categoria_id'] ?? null,
            nome: $validated['nome'] ?? null,
            descricao: $validated['descricao'] ?? null,
            preco_base: $validated['preco_base'] ?? null,
            disponivel_periodo_junino: $validated['disponivel_periodo_junino'] ?? null,
            ativo: $validated['ativo'] ?? null,
        );
    }
    public function toArray(): array
    {
        $data = [
            'categoria_id' => $this->categoria_id,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'preco_base' => $this->preco_base,
            'disponivel_periodo_junino' => $this->disponivel_periodo_junino,
            'ativo' => $this->ativo,
        ];

        return array_filter($data, fn($item) => $item !== null);
    }
}
