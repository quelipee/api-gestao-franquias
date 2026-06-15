<?php

namespace App\DTOs\Unidade;

use App\Enums\TipoUnidade;
use App\Http\Requests\UnidadeRequest;

readonly class UnidadeDTO
{
    public function __construct(
        public ?string      $nome = null,
        public ?string      $cnpj = null,
        public ?string      $cidade = null,
        public ?string      $estado = null,
        public ?string      $endereco = null,
        public ?string      $telefone = null,
        public ?TipoUnidade $tipo = null,
        public ?bool        $ativo = null,
        public ?string      $horario_inicio = null,
        public ?string      $horario_fim = null
    )
    {
    }

    static public function fromRequest(UnidadeRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            nome: $validated['nome'] ?? null,
            cnpj: $validated['cnpj'] ?? null,
            cidade: $validated['cidade'] ?? null,
            estado: $validated['estado'] ?? null,
            endereco: $validated['endereco'] ?? null,
            telefone: $validated['telefone'] ?? null,
            tipo: isset($validated['tipo']) ? (TipoUnidade::tryFrom($validated['tipo']) ?? TipoUnidade::COMPLETA) : null,
            ativo: isset($validated['ativo']) ? filter_var($validated['ativo'], FILTER_VALIDATE_BOOLEAN) : null,
            horario_inicio: $validated['horario_inicio'] ?? null,
            horario_fim: $validated['horario_fim'] ?? null,
        );
    }

    public function toArray(): array
    {
        $data = [
            'nome' => $this->nome,
            'cnpj' => $this->cnpj,
            'cidade' => $this->cidade,
            'estado' => $this->estado,
            'endereco' => $this->endereco,
            'telefone' => $this->telefone,
            'tipo' => $this->tipo?->value,
            'ativo' => $this->ativo,
            'horario_inicio' => $this->horario_inicio,
            'horario_fim' => $this->horario_fim,
        ];

        return array_filter($data, fn ($item) => $item !== null);
    }
}
