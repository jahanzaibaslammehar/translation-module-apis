<?php

namespace App\Services;

use App\Core\Contracts\Services\AbstractServiceInterface;
use App\Core\Services\AbstractService;
use App\Helpers\ResponseCode;
use App\Http\Resources\UserResource;
use App\Responses\UserResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthService extends AbstractService implements AbstractServiceInterface
{
    protected $response;

    public function __construct(Request $request, UserResponse $response)
    {
        parent::__construct($request);
        $this->response = $response;
    }

    public function login(array $request)
    {
        if (!Auth::attempt(['email' => $request['email'] ?? '', 'password' => $request['password'] ?? ''])) {
            throw new AuthenticationException('Invalid credentials');
        }
        $user = Auth::user();
        $token = $user->createToken('API Token')->plainTextToken;
        $user->token = $token;
        $userResource = new UserResource($user);
        $this->response->setResponse(ResponseCode::SUCCESS, 200, $this->response->getUserLoginMessage(), $userResource->toArray($this->request));
        return $this->response;
    }
}