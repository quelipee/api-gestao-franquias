<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'pontos_saldo', 'pontos_acumulados_total', 'pontos_resgatados_total', 'ativo'])]
#[Table('fidelizacoes')]
class Fidelizacao extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'pontos_saldo' => 'integer',
            'pontos_acumulados_total' => 'integer',
            'pontos_resgatados_total' => 'integer',
            'ativo' => 'boolean',
        ];
    }

    protected $attributes = [
        'pontos_saldo' => 0,
        'pontos_acumulados_total' => 0,
        'pontos_resgatados_total' => 0,
        'ativo' => true,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
