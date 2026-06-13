<?php

namespace App\DTOs\Unidade;

use App\Enums\TipoUnidade;
use App\Http\Requests\UnidadeRequest;

readonly class UnidadeDTO
{
    public function __construct(
        public string      $nome,
        public ?string     $cnpj,
        public string      $cidade,
        public string      $estado,
        public string      $endereco,
        public ?string     $telefone,
        public TipoUnidade $tipo,
        public bool        $ativo,
        public ?string     $horario_inicio,
        public ?string     $horario_fim
    )
    {
    }

    static public function fromRequest(UnidadeRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            nome: $validated['nome'],
            cnpj: $validated['cnpj'],
            cidade: $validated['cidade'],
            estado: $validated['estado'],
            endereco: $validated['endereco'],
            telefone: $validated['telefone'],
            tipo: TipoUnidade::tryFrom($validated['tipo'],) ?? TipoUnidade::COMPLETA,
            ativo: filter_var($validated['ativo'], FILTER_VALIDATE_BOOLEAN),
            horario_inicio: $validated['horario_inicio'],
            horario_fim: $validated['horario_fim'],
        );
    }
    public function toArray(): array
    {
        return [
            'nome' => $this->nome,
            'cnpj' => $this->cnpj,
            'cidade' => $this->cidade,
            'estado' => $this->estado,
            'endereco' => $this->endereco,
            'telefone' => $this->telefone,
            'tipo' => $this->tipo->value,
            'ativo' => $this->ativo,
            'horario_inicio' => $this->horario_inicio,
            'horario_fim' => $this->horario_fim,
        ];
    }
}
