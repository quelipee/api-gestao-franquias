<?php

namespace App\Enums;

enum AuditoriaEntidade: string
{
    case Usuario = 'usuarios';
    case Unidade = 'unidades';
    case Produto = 'produtos';
    case CardapioUnidade = 'cardapio_unidade';
    case Estoque = 'estoques';
    case Pedido = 'pedidos';
    case ItemPedido = 'itens_pedido';
    case Pagamento = 'pagamentos';
    case Fidelizacao = 'fidelizacoes';

    public function label(): string
    {
        return match ($this) {
            self::Usuario => 'Usuário',
            self::Unidade => 'Unidade',
            self::Produto => 'Produto',
            self::CardapioUnidade => 'Cardápio da Unidade',
            self::Estoque => 'Estoque',
            self::Pedido => 'Pedido',
            self::ItemPedido => 'Item do Pedido',
            self::Pagamento => 'Pagamento',
            self::Fidelizacao => 'Fidelização',
        };
    }
}
