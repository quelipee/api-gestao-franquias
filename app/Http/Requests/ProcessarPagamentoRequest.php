<?php

namespace App\Http\Requests;

use App\Enums\PagamentoStatus;
use App\Enums\TipoPagamento;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProcessarPagamentoRequest extends FormRequest
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
            'forma_pagamento' => ['required', 'string', Rule::enum(TipoPagamento::class)],
            'simular_resultado' => ['nullable', 'string', Rule::enum(PagamentoStatus::class)],
        ];
    }
}
