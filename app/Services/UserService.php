<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use App\Models\User;
use Illuminate\Http\Response;

class UserService
{
    public function login(array $data): ?array
    {
        return [];
    }

    public function register(array $data): ?array
    {
        $user = User::whereEmail($data['email'])->first();

        if ($user) {
            throw new ServiceException('User already exists', Response::HTTP_BAD_REQUEST);
        }

        $user = User::create($data);

        return $user->getAttributes();
    }
}