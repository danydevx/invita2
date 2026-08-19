<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityService
{
    public function log(string $type, array $options = []): Activity
    {
        $user = $options['user'] ?? null;
        $actor = $options['actor'] ?? null;
        $subject = $options['subject'] ?? null;
        $description = $options['description'] ?? null;
        $metadata = $options['metadata'] ?? null;
        $request = $options['request'] ?? null;

        return Activity::create([
            'user_id' => $this->idFrom($user),
            'actor_id' => $this->idFrom($actor),
            'type' => $type,
            'description' => $description,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'ip_address' => $this->ipFrom($request),
            'user_agent' => $this->userAgentFrom($request),
            'metadata' => $metadata,
        ]);
    }

    private function idFrom(?User $user): ?int
    {
        return $user?->getKey();
    }

    private function ipFrom(?Request $request): ?string
    {
        return $request?->ip();
    }

    private function userAgentFrom(?Request $request): ?string
    {
        return $request?->userAgent();
    }
}
