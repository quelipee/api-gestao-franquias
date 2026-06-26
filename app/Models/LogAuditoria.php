<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'acao', 'entidade', 'entidade_id', 'dados_anteriores', 'dados_novos', 'ip', 'user_agent',])]
#[Table('logs_auditoria')]
class LogAuditoria extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'entidade_id' => 'integer',
            'dados_anteriores' => 'array',
            'dados_novos' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
