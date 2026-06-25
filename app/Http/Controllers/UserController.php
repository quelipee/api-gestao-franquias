<?php

namespace App\Http\Controllers;

use App\Contracts\Services\FidelizacaoServiceContract;
use App\Contracts\Services\UserAuthContract;
use App\DTOs\UserAuthDTO;
use App\DTOs\UserRegistrationDTO;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserSignInRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserController extends Controller
{
    public function __construct(
        protected UserAuthContract           $userAuth,
        protected FidelizacaoServiceContract $fidelizacaoService
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
        return $this->userAuth->authenticate(UserAuthDTO::fromRequest($request));
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso!'
        ], ResponseAlias::HTTP_OK);
    }
}
