<?php

namespace App\Models;

use App\Enums\CanalPedido;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Random\RandomException;

#[Fillable(['unidade_id', 'user_id', 'canal_pedido', 'subtotal', 'desconto', 'total', 'observacao',
    'cancelado_em', 'motivo_cancelamento',
    'status'
])]
#[Table('pedidos')]
class Pedido extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Pedido $pedido) {
            if (empty($pedido->numero_pedido)) {
                $pedido->numero_pedido = static::gerarNumeroPedido();
            }

            $pedido->status ??= OrderStatus::AguardandoPagamento;
            $pedido->canal_pedido ??= CanalPedido::App;
            $pedido->desconto ??= 0;
        });
    }

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'desconto' => 'decimal:2',
            'total' => 'decimal:2',
            'cancelado_em' => 'datetime',
            'status' => OrderStatus::class,
            'canal_pedido' => CanalPedido::class,
        ];
    }

    public function unidade(): BelongsTo
    {
        return $this->belongsTo(Unidade::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function itens(): HasMany
    {
        return $this->hasMany(ItemPedido::class);
    }

    private static function gerarNumeroPedido(): string
    {
        return 'PED-' . Str::upper(Str::ulid());
    }
}
