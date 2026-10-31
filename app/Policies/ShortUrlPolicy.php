<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\ShortUrl;
use App\Models\User;

class ShortUrlPolicy
{
    
    public function viewAny(User $user): bool
    {
        return true;
    }

    
    public function view(User $user, ShortUrl $shortUrl): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isAdmin()) {
            return (int) $user->company_id === (int) $shortUrl->company_id;
        }

        // Member
        return (int) $user->company_id === (int) $shortUrl->company_id
            && (int) $user->id === (int) $shortUrl->user_id;
    }

    
    public function create(User $user): bool
    {
        return in_array($user->role, [UserRole::Admin, UserRole::Member], true);
    }

    
    public function delete(User $user, ShortUrl $shortUrl): bool
    {
        if ($user->isAdmin()) {
            return (int) $user->company_id === (int) $shortUrl->company_id;
        }

        return (int) $user->company_id === (int) $shortUrl->company_id
            && (int) $user->id === (int) $shortUrl->user_id;
    }
}

