<?php

namespace App\application\Auditoria;

use App\Contracts\Services\AuditoriaServiceContract;
use App\Enums\AuditoriaAcao;
use App\Enums\AuditoriaEntidade;
use App\Models\LogAuditoria;

class AuditoriaService implements AuditoriaServiceContract
{
    public function registrar(
        ?int    $user_id,
        AuditoriaAcao  $acao,
        ?AuditoriaEntidade $entidade = null,
        ?int    $entidadeId = null,
        ?array  $dadosAnteriores = null,
        ?array  $dadosNovos = null,
    ): void
    {
        LogAuditoria::create([
            'user_id' => $user_id,
            'acao' => $acao->value,
            'entidade' => $entidade->value,
            'entidade_id' => $entidadeId,
            'dados_anteriores' => $dadosAnteriores,
            'dados_novos' => $dadosNovos,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
