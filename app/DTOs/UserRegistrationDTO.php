<?php

namespace App\DTOs;

use App\Http\Requests\UserRegisterRequest;

readonly class UserRegistrationDTO
{
    public function __construct(
        public string  $name,
        public string  $email,
        public string  $password,
        public string  $role,
        public ?string $cpf = null,
        public bool    $ativo = true,
        public bool    $consentimento_lgpd = false,
    )
    {
    }

    public static function fromValidatedRequest(UserRegisterRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            name: $validated['name'],
            email: $validated['email'],
            password: $validated['password'],
            role: $validated['role'],
            cpf: $validated['cpf'] ?? null,
            ativo: $validated['ativo'] ?? true,
            consentimento_lgpd: $validated['consentimento_lgpd'] ?? false,
        );
    }
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
            'cpf' => $this->cpf,
            'ativo' => $this->ativo,
            'consentimento_lgpd' => $this->consentimento_lgpd,
            'consentimento_lgpd_em' => $this->consentimento_lgpd ? now() : null,
        ];
    }
}
