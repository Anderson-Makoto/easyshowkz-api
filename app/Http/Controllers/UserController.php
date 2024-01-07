<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Helpers\ResponseErrorHandlerHelper;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function login(UserLoginRequest $request)
    {
        try {
            $requestData = $request->all();
            $this->userService->login($requestData);
        } catch (Exception $e) {
            return ResponseErrorHandlerHelper::handle($e);
        }
    }

    public function register(UserRegisterRequest $request): JsonResponse
    {
        try {
            $requestData = $request->all();
            $userData = $this->userService->register($requestData);
            return response()->json($userData);
        } catch (Exception $e) {
            return ResponseErrorHandlerHelper::handle($e);
        }
    }
}
