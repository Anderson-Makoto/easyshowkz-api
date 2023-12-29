<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSession;

class UserSessionService
{
    public function saveSession(User $user): ?array
    {
        if (UserSession::whereUserId($user->id)->exists()) {

        }

        return null;
    }
}