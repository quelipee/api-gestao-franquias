<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['produto_id','unidade_id','disponivel'])]
#[Table('cardapio_unidade')]
class CardapioUnidade extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
          'disponivel' => 'boolean'
        ];
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }

    public function unidade(): BelongsToMany
    {
        return $this->belongsToMany(
            Unidade::class,
            'cardapio_unidade',
            'produto_id',
            'unidade_id'
        )->using(CardapioUnidade::class)
            ->withPivot('disponivel')
            ->withTimestamps();
    }
}
