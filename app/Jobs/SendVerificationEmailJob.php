<?php

namespace App\Jobs;

use App\Models\User;

class SendVerificationEmailJob
{
    public function __construct(public int $userId)
    {}

    public function handle(): void
    {
        $user = User::query()->find($this->userId);
        if (! $user) {
            return;
        }

        $user->sendEmailVerificationNotification();
    }

    public static function dispatch(int $userId): void
    {
        (new self($userId))->handle();
    }
}
