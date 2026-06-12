<?php

namespace App\Infrastructure\Repository;

use App\Contracts\Repository\UserRepositoryContract;
use App\DTOs\UserRegistrationDTO;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

class UserRepository implements UserRepositoryContract
{

    /**
     * @throws Throwable
     */
    public function create(UserRegistrationDTO $dto) : User
    {
        return DB::transaction(function () use ($dto) {
           return User::create($dto->toArray());
        });
    }

    public function findByEmail(string $email): ?User
    {
        return User::query()->where('email', $email)->first();
    }
}
