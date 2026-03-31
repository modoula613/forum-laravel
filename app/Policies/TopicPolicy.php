<?php

namespace App\Policies;

use App\Models\Topic;
use App\Models\User;

class TopicPolicy
{
    public function delete(User $user, Topic $topic): bool
    {
        return $user->id === $topic->user_id || $user->role === 'admin';
    }
}
