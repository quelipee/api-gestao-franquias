<?php

namespace App\DTOs\Estoque;

use App\Enums\TipoMovimentacaoEstoque;
use App\Http\Requests\EstoqueMovimentacaoRequest;

readonly class MovimentacaoEstoqueDTO
{
    public function __construct(
        public ?int                     $estoque_id = null,
        public ?TipoMovimentacaoEstoque $tipo = null,
        public ?int                     $quantidade = null,
        public ?string                  $motivo = null,
        public ?int                     $user_id = null,
    )
    {
    }

    public static function fromRequest(EstoqueMovimentacaoRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            estoque_id: $validated['estoque_id'] ?? null,
            tipo: isset($validated['tipo']) ? TipoMovimentacaoEstoque::from($validated['tipo']) : null,
            quantidade: $validated['quantidade'] ?? null,
            motivo: $validated['motivo'] ?? null,
            user_id: auth()->id(),
        );
    }

    public function toArray(): array
    {
        $data = [
            'estoque_id' => $this->estoque_id,
            'tipo' => $this->tipo?->value,
            'quantidade' => $this->quantidade,
            'motivo' => $this->motivo,
            'user_id' => $this->user_id,
        ];

        return array_filter($data, fn($value) => $value !== null);
    }
}
