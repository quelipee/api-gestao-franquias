<?php

namespace App\Contracts\Services;

use App\DTOs\UserAuthDTO;
use App\DTOs\UserRegistrationDTO;
use App\Models\User;

interface UserAuthContract
{
    public function authenticate(UserAuthDTO $userAuthDTO);
    public function signUp(UserRegistrationDTO $dto) : User;
}
