<?php

namespace App\application\Authenticated;

use App\Contracts\Repository\UserRepositoryContract;
use App\Contracts\Services\UserAuthContract;
use App\DTOs\UserAuthDTO;
use App\DTOs\UserRegistrationDTO;
use App\Exceptions\UserAlreadyExistsException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserAuthenticated implements UserAuthContract
{
    public function __construct(
        protected UserRepositoryContract $userRepository,
    )
    {
    }

    /**
     * @throws UserAlreadyExistsException
     */
    public function authenticate(UserAuthDTO $userAuthDTO) : array {
        $user = $this->userRepository->findByEmail($userAuthDTO->email);

        if (!$user || !Hash::check($userAuthDTO->password, $user->password)) {
            throw UserAlreadyExistsException::InvalidPassword();
        }
        $token = $user->createToken('token')->plainTextToken;

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'token' => $token,
        ];
    }

    /**
     * @throws UserAlreadyExistsException
     */
    public function signUp(UserRegistrationDTO $dto) : User
    {
        if ($this->emailExists($dto->email)) {
            throw UserAlreadyExistsException::EmailAlreadyExists();
        }

        return $this->userRepository->create($dto);
    }

    private function emailExists(string $email) : bool
    {
        return User::where('email', $email)->exists();
    }
}
