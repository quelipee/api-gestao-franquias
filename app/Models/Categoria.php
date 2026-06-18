<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[fillable(['nome', 'descricao'])]
#[Table('categorias')]
class Categoria extends Model
{
    use HasFactory;

    public function produtos(): HasMany
    {
        return $this->hasMany(Produto::class);
    }
}
