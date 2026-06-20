<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EstoqueRequest extends FormRequest
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
            'unidade_id' => ['required', 'integer', 'exists:unidades,id'],
            'produto_id' => ['required', 'integer', 'exists:produtos,id',
                Rule::unique('estoques')->where(
                    fn($query) => $query->where('unidade_id', $this->unidade_id)
                ),
            ],
            'quantidade' => ['required', 'integer', 'min:0'],
            'quantidade_minima' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'unidade_id.required' => 'A unidade é obrigatória.',
            'unidade_id.exists' => 'A unidade informada não existe.',

            'produto_id.required' => 'O produto é obrigatório.',
            'produto_id.exists' => 'O produto informado não existe.',

            'quantidade.required' => 'A quantidade é obrigatória.',
            'quantidade.integer' => 'A quantidade deve ser um número inteiro.',
            'quantidade.min' => 'A quantidade não pode ser negativa.',

            'quantidade_minima.required' => 'A quantidade mínima é obrigatória.',
            'quantidade_minima.integer' => 'A quantidade mínima deve ser um número inteiro.',
            'quantidade_minima.min' => 'A quantidade mínima não pode ser negativa.',
        ];
    }
}
