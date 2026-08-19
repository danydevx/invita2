<?php

namespace App\Services;

use App\Models\SystemAnnouncement;
use App\Models\User;
use App\Models\UserAnnouncementState;
use Illuminate\Support\Collection;

class SystemAnnouncementService
{
    public function activeForUser(User $user, bool $markSeen = true): Collection
    {
        // Evita cargar anuncios si el modulo esta desactivado.
        if (! app(ModuleService::class)->isEnabled('announcements')) {
            return collect();
        }

        $audiences = $this->audiencesForUser($user);

        $announcements = SystemAnnouncement::query()
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->whereIn('audience', $audiences)
            ->where(function ($query) use ($user) {
                $query->where('dismissible', false)
                    ->orWhereDoesntHave('states', function ($stateQuery) use ($user) {
                        $stateQuery->where('user_id', $user->id)
                            ->whereNotNull('dismissed_at');
                    });
            })
            ->orderByRaw("CASE priority WHEN 'critical' THEN 4 WHEN 'high' THEN 3 WHEN 'normal' THEN 2 WHEN 'low' THEN 1 ELSE 0 END DESC")
            ->orderByDesc('starts_at')
            ->orderByDesc('id')
            ->get();

        if ($markSeen && $announcements->isNotEmpty()) {
            foreach ($announcements as $announcement) {
                UserAnnouncementState::updateOrCreate([
                    'user_id' => $user->id,
                    'system_announcement_id' => $announcement->id,
                ], [
                    'seen_at' => now(),
                ]);
            }
        }

        return $announcements;
    }

    public function dismiss(User $user, SystemAnnouncement $announcement): bool
    {
        if (! $announcement->dismissible) {
            return false;
        }

        if (! $this->isApplicableToUser($user, $announcement)) {
            return false;
        }

        UserAnnouncementState::updateOrCreate([
            'user_id' => $user->id,
            'system_announcement_id' => $announcement->id,
        ], [
            'dismissed_at' => now(),
        ]);

        return true;
    }

    public function isApplicableToUser(User $user, SystemAnnouncement $announcement): bool
    {
        if (! $announcement->is_active) {
            return false;
        }

        if ($announcement->starts_at && $announcement->starts_at->isFuture()) {
            return false;
        }

        if ($announcement->ends_at && $announcement->ends_at->isPast()) {
            return false;
        }

        return in_array($announcement->audience, $this->audiencesForUser($user), true);
    }

    private function audiencesForUser(User $user): array
    {
        $audiences = ['all'];

        if ($user->hasRole('member')) {
            $audiences[] = 'members';
        }

        if ($user->hasAnyRole(['admin', 'superadmin'])) {
            $audiences[] = 'admins';
        }

        return $audiences;
    }
}
