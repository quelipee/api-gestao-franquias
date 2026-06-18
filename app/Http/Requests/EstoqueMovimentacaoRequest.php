<?php

namespace App\Http\Requests;

use App\Enums\TipoMovimentacaoEstoque;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EstoqueMovimentacaoRequest extends FormRequest
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
            'estoque_id' => ['required', 'integer', 'exists:estoques,id'],
            'tipo' => ['required', 'string', Rule::enum(TipoMovimentacaoEstoque::class)],
            'quantidade' => ['required', 'integer', 'min:1',],
            'motivo' => ['nullable', 'string', 'max:255',],
        ];
    }

    public function messages(): array
    {
        return [
            'estoque_id.required' => 'O estoque é obrigatório.',
            'estoque_id.exists' => 'Estoque não encontrado.',

            'tipo.required' => 'O tipo de movimentação é obrigatório.',
            'tipo.in' => 'Tipo de movimentação inválido.',

            'quantidade.required' => 'A quantidade é obrigatória.',
            'quantidade.min' => 'A quantidade deve ser maior que zero.',

            'motivo.max' => 'O motivo pode ter no máximo 255 caracteres.',
        ];
    }
}
