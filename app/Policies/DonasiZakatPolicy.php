<?php

namespace App\Policies;

use App\Models\DonasiZakat;
use App\Models\User;

class DonasiZakatPolicy
{
    public function view(User $user, DonasiZakat $donasi): bool
    {
        return $user->id === $donasi->user_id;
    }

    public function update(User $user, DonasiZakat $donasi): bool
    {
        return $user->id === $donasi->user_id && $donasi->status === 'draft';
    }
}
