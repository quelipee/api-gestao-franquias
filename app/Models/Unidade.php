<?php

namespace App\Models;

use App\Enums\TipoUnidade;
use Database\Factories\UnidadeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['nome', 'cnpj', 'cidade', 'estado'
    , 'endereco', 'telefone', 'tipo', 'ativo', 'horario_inicio', 'horario_fim'])]
class Unidade extends Model
{
    /** @use HasFactory<UnidadeFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
            'tipo' => TipoUnidade::class,
            'horario_inicio' => 'datetime:H:i:s',
            'horario_fim' => 'datetime:H:i:s',
            'deleted_at' => 'datetime',
        ];
    }

    public function produtos(): BelongsToMany
    {
        return $this->belongsToMany(Produto::class,
            'cardapio_unidade', 'unidade_id', 'produto_id')
            ->withPivot('disponivel')
            ->withTimestamps();
    }
}
