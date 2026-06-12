<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserRegisterRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'cpf' => ['nullable', 'string', 'size:11', 'unique:users,cpf'],
            'role' => ['nullable', 'string', 'in:cliente,ADMIN,FUNCIONARIO'],
            'ativo' => ['nullable', 'boolean'],
            'consentimento_lgpd' => ['nullable', 'boolean'],
            'consentimento_lgpd_em' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Insira um e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'cpf.size' => 'O CPF deve conter exatamente 11 dígitos (apenas números).',
            'cpf.unique' => 'Este CPF já está cadastrado.',
        ];
    }
}
