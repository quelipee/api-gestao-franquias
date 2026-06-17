<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['categoria_id', 'nome', 'descricao', 'preco_base',
    'disponivel_periodo_junino', 'ativo'])]
#[Table('produtos')]
class Produto extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'preco_base' => 'decimal:2',
            'disponivel_periodo_junino' => 'boolean',
            'ativo' => 'boolean',
        ];
    }
    public function categoria(): belongsTo {
        return $this->belongsTo(Categoria::class);
    }
}
