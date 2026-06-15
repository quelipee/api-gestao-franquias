<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnidadeClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'nome' => $this->nome,
            'cidade' => $this->cidade,
            'estado' => $this->estado,
            'endereco' => $this->endereco,
            'telefone' => $this->telefone,
            'tipo' => $this->tipo,
            'horario_inicio' => $this->horario_inicio,
            'horario_fim' => $this->horario_fim,
        ];
    }
}
