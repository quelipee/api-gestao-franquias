<?php

namespace App\DTOs\Pagamento;

use App\Enums\PagamentoStatus;
use App\Enums\TipoPagamento;
use App\Http\Requests\ProcessarPagamentoRequest;

readonly class ProcessarPagamentoDTO
{
    public function __construct(
        public TipoPagamento    $forma_pagamento,
        public ?PagamentoStatus $simular_resultado = null
    )
    {
    }

    public static function fromRequest(ProcessarPagamentoRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            forma_pagamento: TipoPagamento::from($validated['forma_pagamento']),
            simular_resultado: isset($validated['simular_resultado'])
                ? PagamentoStatus::from($validated['simular_resultado'])
                : null,
        );
    }

    public function toArray(): array
    {
        $data = [
            'forma_pagamento' => $this->forma_pagamento,
            'simular_resultado' => $this->simular_resultado,
        ];

        return array_filter($data, fn($value) => $value !== null);
    }
}
