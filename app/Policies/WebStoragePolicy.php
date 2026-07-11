<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WebStorage;

class WebStoragePolicy
{
    public function view(User $user, WebStorage $webStorage): bool
    {
        return $user->id === $webStorage->user_id;
    }

    public function delete(User $user, WebStorage $webStorage): bool
    {
        return $user->id === $webStorage->user_id;
    }
}
