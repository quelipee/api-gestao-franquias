<?php

namespace App\Http\Controllers;

use App\Contracts\Services\UserAuthContract;
use App\DTOs\UserAuthDTO;
use App\DTOs\UserRegistrationDTO;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserSignInRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserController extends Controller
{
    public function __construct(
        protected UserAuthContract $userAuth
    )
    {
    }

    public function register(UserRegisterRequest $request)
    {
        $user = $this->userAuth->signUp(UserRegistrationDTO::fromValidatedRequest($request));

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], ResponseAlias::HTTP_CREATED);
    }

    public function login(UserSignInRequest $request)
    {
        $this->userAuth->authenticate(UserAuthDTO::fromValidatedRequest($request));
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ], ResponseAlias::HTTP_OK);
    }
}
