<?php

namespace App\Http\Requests;

use App\Enums\TipoUnidade;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UnidadeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:100'],
            'cnpj' => ['nullable', 'string', 'max:14'],
            'cidade' => ['required', 'string', 'max:100'],
            'estado' => ['required', 'string', 'size:2'],
            'endereco' => ['required', 'string', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'tipo' => ['nullable', Rule::enum(TipoUnidade::class)],
            'ativo' => ['nullable', 'boolean'],
            'horario_inicio' => ['nullable', 'date_format:H:i:s'],
            'horario_fim' => ['nullable', 'date_format:H:i:s', 'after:horario_inicio'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nome' => 'nome da unidade',
            'cnpj' => 'CNPJ',
            'cidade' => 'cidade',
            'estado' => 'estado (UF)',
            'endereco' => 'endereço',
            'telefone' => 'telefone',
            'tipo' => 'tipo de unidade',
            'ativo' => 'status ativo',
            'horario_inicio' => 'horário de início',
            'horario_fim' => 'horário de término',
        ];
    }
}
