<?php

namespace App\Exceptions;

use Exception;

class UserAlreadyExistsException extends Exception
{
    public static function EmailAlreadyExists(): self
    {
        return new self("User with this email already exists.");
    }

    public static function InvalidPassword(): self
    {
        return new self("Invalid password.");
    }
}
