<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['unidade_id', 'produto_id', 'quantidade', 'quantidade_minima'])]
#[Table('estoques')]
class Estoque extends Model
{
    public function unidade(): BelongsTo
    {
        return $this->belongsTo(Unidade::class);
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }
}
