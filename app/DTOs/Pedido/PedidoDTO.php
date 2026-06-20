<?php

namespace App\DTOs\Pedido;

use App\Enums\CanalPedido;
use App\Http\Requests\PedidoRequest;

readonly class PedidoDTO
{
    public function __construct(
        public ?int    $unidade_id = null,
        public ?CanalPedido $canal_pedido = null,
        public array   $itens = [],
        public ?int    $user_id = null,
    )
    {
    }

    public static function fromRequest(PedidoRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            unidade_id: $validated['unidade_id'] ?? null,
            canal_pedido: CanalPedido::from($validated['canal_pedido']),
            itens: collect($validated['itens'])
                ->map(fn(array $item) => PedidoItemDTO::fromArray($item))
                ->all(),
            user_id: auth()->id(),
        );
    }

    public function toArray(): array
    {
        $data = [
            'unidade_id' => $this->unidade_id,
            'canal_pedido' => $this->canal_pedido,
            'itens' => array_map(fn(PedidoItemDTO $item) => $item->toArray(), $this->itens),
            'user_id' => $this->user_id,
        ];

        return array_filter($data, fn($value) => $value !== null);
    }
}
