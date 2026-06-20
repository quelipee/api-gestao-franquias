<?php

namespace App\Http\Requests;

use App\Enums\CanalPedido;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PedidoRequest extends FormRequest
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
            'canal_pedido' => ['required', 'string', Rule::enum(CanalPedido::class)],
            'itens' => ['required', 'array', 'min:1'],
            'itens.*.produto_id' => ['required', 'integer', 'exists:produtos,id'],
            'itens.*.quantidade' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'unidade_id.required' => 'A unidade é obrigatória.',
            'unidade_id.exists' => 'A unidade informada não existe.',

            'canal_pedido.required' => 'O canal do pedido é obrigatório.',
            'canal_pedido.in' => 'Canal do pedido inválido.',

            'itens.required' => 'O pedido deve possuir pelo menos um item.',
            'itens.array' => 'Os itens devem ser enviados em formato de lista.',
            'itens.min' => 'O pedido deve possuir pelo menos um item.',

            'itens.*.produto_id.required' => 'O produto é obrigatório.',
            'itens.*.produto_id.exists' => 'O produto informado não existe.',

            'itens.*.quantidade.required' => 'A quantidade é obrigatória.',
            'itens.*.quantidade.min' => 'A quantidade deve ser maior que zero.',
        ];
    }
}
