<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProdutoRequest extends FormRequest
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
        $estaAtualizado = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $regra = $estaAtualizado ? ['sometimes', 'required'] : ['required'];

        return [
            'categoria_id' => array_merge($regra, ['exists:categorias,id']),
            'nome' => array_merge($regra, ['string', 'max:120']),
            'descricao' => array_merge(['nullable'], $estaAtualizado ? ['sometimes'] : [], ['string', 'max:255']),
            'preco_base' => array_merge($regra, ['numeric', 'min:0']),
            'disponivel_periodo_junino' => ['sometimes', 'boolean'],
            'ativo' => ['sometimes', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'categoria_id' => 'categoria',
            'nome' => 'nome',
            'descricao' => 'descrição',
            'preco_base' => 'preço base',
            'disponivel_periodo_junino' => 'disponibilidade no período junino',
            'ativo' => 'status',
        ];
    }
}
