<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function login(array $data): ?Model
    {
        $user = $this->getUserIfValidCredentials($data['email'], $data['password']);

        Auth::login($user);
        $user->refresh();

        return $user;
    }

    public function register(array $data): ?Model
    {
        $user = User::make($data);
        $user->password = Hash::make($data['password']);

        $user->save();

        return $user;
    }

    private function getUserIfValidCredentials(string $email, string $password): User
    {
        $credentials = [
            'email' => $email,
            'password' => $password
        ];

        if (!Auth::attempt($credentials)) {
            throw new ServiceException('Email or password invalid');
        }

        $user = User::whereEmail($email)->first();

        return $user;
    }
}