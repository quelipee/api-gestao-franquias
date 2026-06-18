<?php

namespace App\Models;

use App\Enums\TipoMovimentacaoEstoque;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['estoque_id', 'user_id', 'tipo', 'quantidade', 'motivo'])]
#[Table('movimentacoes_estoque')]
class EstoqueMovimentacao extends Model
{
    public function casts(): array
    {
        return [
            'tipo' => TipoMovimentacaoEstoque::class
        ];
    }

    public function estoque(): BelongsTo
    {
        return $this->belongsTo(Estoque::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
