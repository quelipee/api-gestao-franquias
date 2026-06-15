<?php

namespace App\DTOs;

use App\Http\Requests\UserSignInRequest;

readonly class UserAuthDTO
{
    public function __construct(
        public string $email,
        public string $password
    )
    {
    }

    public static function fromRequest(UserSignInRequest $request) : self
    {
        $validated = $request->validated();

        return new self(
            email: $validated['email'],
            password: $validated['password']
        );
    }
}
