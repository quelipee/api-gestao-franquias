<?php

namespace App\Contracts\Services;

use App\Enums\AuditoriaAcao;
use App\Enums\AuditoriaEntidade;

interface AuditoriaServiceContract
{
    public function registrar(?int   $user_id, AuditoriaAcao $acao, ?AuditoriaEntidade $entidade = null, ?int $entidadeId = null,
                              ?array $dadosAnteriores = null, ?array $dadosNovos = null): void;
}
