<?php

namespace App\DTOs;

use App\Http\Requests\UserSignInRequest;

class UserAuthDTO
{
    public function __construct(
        public string $email,
        public string $password
    )
    {
    }

    public static function fromValidatedRequest(UserSignInRequest $request) : self
    {
        return new self(
            email: $request->validated('email'),
            password: $request->validated('password')
        );
    }
}
