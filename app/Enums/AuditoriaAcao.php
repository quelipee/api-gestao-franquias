<?php

namespace App\Enums;

enum AuditoriaAcao: string
{
    case UsuarioRegistrado = 'USUARIO_REGISTRADO';
    case Login = 'LOGIN';
    case Logout = 'LOGOUT';

    case PedidoCriado = 'PEDIDO_CRIADO';
    case StatusAtualizado = 'STATUS_ATUALIZADO';
    case PedidoCancelado = 'PEDIDO_CANCELADO';

    case PagamentoAprovado = 'PAGAMENTO_APROVADO';
    case PagamentoRecusado = 'PAGAMENTO_RECUSADO';

    case EstoqueEntrada = 'ESTOQUE_ENTRADA';
    case EstoqueSaida = 'ESTOQUE_SAIDA';

    case PontosAcumulados = 'PONTOS_ACUMULADOS';
    case PontosResgatados = 'PONTOS_RESGATADOS';

    case UnidadeCriada = 'UNIDADE_CRIADA';
    case UnidadeAtualizada = 'UNIDADE_ATUALIZADA';
    case UnidadeDesativada = 'UNIDADE_DESATIVADA';

    case ProdutoCriado = 'PRODUTO_CRIADO';
    case ProdutoAtualizado = 'PRODUTO_ATUALIZADO';

    public function label(): string
    {
        return match ($this) {
            self::UsuarioRegistrado => 'Usuário registrado',
            self::Login => 'Login realizado',
            self::Logout => 'Logout realizado',

            self::PedidoCriado => 'Pedido criado',
            self::StatusAtualizado => 'Status do pedido atualizado',
            self::PedidoCancelado => 'Pedido cancelado',

            self::PagamentoAprovado => 'Pagamento aprovado',
            self::PagamentoRecusado => 'Pagamento recusado',

            self::EstoqueEntrada => 'Entrada de estoque',
            self::EstoqueSaida => 'Saída de estoque',

            self::PontosAcumulados => 'Pontos de fidelização acumulados',
            self::PontosResgatados => 'Pontos de fidelização resgatados',

            self::UnidadeCriada => 'Unidade criada',
            self::UnidadeAtualizada => 'Unidade atualizada',
            self::UnidadeDesativada => 'Unidade desativada',

            self::ProdutoCriado => 'Produto criado',
            self::ProdutoAtualizado => 'Produto atualizado',
        };
    }
}
