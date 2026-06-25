<?php

namespace App\Models;

use App\Enums\TipoTransacaoFidelizacao;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


#[Fillable(['fidelizacao_id', 'pedido_id', 'tipo', 'pontos', 'descricao'])]
#[Table('transacoes_fidelizacao')]
class TransacaoFidelizacao extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'tipo' => TipoTransacaoFidelizacao::class,
            'pontos' => 'integer',
        ];
    }

    public function fidelizacao(): BelongsTo
    {
        return $this->belongsTo(Fidelizacao::class);
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }
}
