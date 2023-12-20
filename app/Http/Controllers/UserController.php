<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use Exception;
use function App\Helpers\responseErrorHandler;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except([
            'login',
        ]);
    }

    public function login(UserLoginRequest $request)
    {
        try {

        } catch (Exception $e) {
            return responseErrorHandler($e);
        }
    }
}
