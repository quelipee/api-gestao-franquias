<?php

namespace App\Contracts\Repository;

use App\DTOs\UserRegistrationDTO;
use App\Models\User;

interface UserRepositoryContract
{
    public function create(UserRegistrationDTO $dto): User;

    public function findByEmail(string $email): ?User;
}
