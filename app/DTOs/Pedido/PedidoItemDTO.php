<?php

namespace App\DTOs\Pedido;

class PedidoItemDTO
{
    public function __construct(
        public int $produto_id,
        public int $quantidade,
    )
    {
    }

    //TODO adjust
    public static function fromArray(array $item): self
    {
        return new self(
            produto_id: $item['produto_id'],
            quantidade: $item['quantidade'],
        );
    }

    public function toArray(): array
    {
        return [
            'produto_id' => $this->produto_id,
            'quantidade' => $this->quantidade,
        ];
    }
}
