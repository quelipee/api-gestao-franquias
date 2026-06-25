<?php

namespace App\Models;

use App\Enums\PagamentoStatus;
use App\Enums\TipoPagamento;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['pedido_id', 'forma_pagamento', 'status', 'valor', 'gateway_transaction_id',
    'gateway_status', 'gateway_payload', 'aprovado_em', 'recusado_em'])]
#[Table('pagamentos')]
class Pagamento extends Model
{
    use HasFactory;
    protected function casts(): array
    {
        return [
            'forma_pagamento' => TipoPagamento::class,
            'status' => PagamentoStatus::class,
            'valor' => 'decimal:2',
            'gateway_payload' => 'array',
            'aprovado_em' => 'datetime',
            'recusado_em' => 'datetime',
        ];
    }

    protected $attributes = [
        'status' => PagamentoStatus::Pendente->value,
        'forma_pagamento' => TipoPagamento::MOCK,
    ];

    protected static function booted(): void
    {
        static::creating(function (Pagamento $pagamento) {
            if (empty($pagamento->gateway_transaction_id)) {
                $pagamento->gateway_transaction_id = 'MOCK-' . uniqid();
            }
        });
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }
}
