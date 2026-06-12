<?php

namespace App\Models;

use Database\Factories\UnidadeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nome', 'cidade', 'estado', 'ativo'])]
class Unidade extends Model
{
    /** @use HasFactory<UnidadeFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['ativo' => 'boolean'];
    }

    public function estoques()
    {
        return $this->hasMany(Estoque::class);
    }

    public function getNomeUppercaseAttribute(): string
    {
        return strtoupper($this->nome);
    }

    public function setEstadoAttribute($value): void
    {
        $this->attributes['estado'] = strtoupper($value);
    }
}
