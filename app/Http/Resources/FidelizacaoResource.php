<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FidelizacaoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'saldo' => $this->pontos_saldo,
            'total_acumulado' => $this->pontos_acumulados_total,
            'total_resgatado' => $this->pontos_resgatados_total,
            'ativo' => $this->ativo,
        ];
    }
}
