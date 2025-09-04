<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthService $service)
    {
        
    }
    
    public function login(LoginRequest $request)
    {
        try {
            $response = $this->service->login($request->only('email', 'password'));
            return response()->sendResponse($response->getResponseType(), $response->code(), $response->message(), $response->getData());
        } catch (AuthenticationException $e) {
            return response()->sendResponse('ERROR', 401, $e->getMessage(), $e->getMessage(), 401);
        }
    }
}
