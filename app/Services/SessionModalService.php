<?php

namespace App\Services;

use App\Models\SessionModal;
use App\Models\UserModalDismissal;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class SessionModalService
{
    /**
     * Return all modals that should be shown to the given user on the given route.
     */
    public function getModalsForUser(?User $user, string $currentRouteName = ''): Collection
    {
        $modals = SessionModal::active()
            ->orderBy('sort_order')
            ->get();

        return $modals->filter(function (SessionModal $modal) use ($user, $currentRouteName) {
            return $this->shouldShow($modal, $user, $currentRouteName);
        })->values();
    }

    public function shouldShow(SessionModal $modal, ?User $user, string $currentRouteName = ''): bool
    {
        // Date range check
        if (! $modal->isWithinDateRange()) {
            return false;
        }

        $conditions = $modal->conditions ?? [];

        // Audience check
        $audience = $conditions['audience'] ?? 'all';
        if ($audience === 'guests_only' && $user !== null) {
            return false;
        }
        if ($audience === 'logged_in_only' && $user === null) {
            return false;
        }

        // Page restriction check
        $allowedPages = $conditions['pages'] ?? [];
        if (! empty($allowedPages) && $currentRouteName && ! in_array($currentRouteName, $allowedPages, true)) {
            return false;
        }

        // The following conditions require a logged-in user — skip for guests
        if ($user !== null) {
            // Condition: new players only
            if (! empty($conditions['new_players_only'])) {
                $days = (int) ($conditions['new_players_days'] ?? 7);
                if ($user->created_at === null || $user->created_at->lt(Carbon::now()->subDays($days))) {
                    return false;
                }
            }

            // Condition: minimum character level
            $minLevel = $conditions['min_character_level'] ?? null;
            if ($minLevel !== null && $minLevel > 0) {
                $maxLevel = $this->getUserMaxCharacterLevel($user);
                if ($maxLevel < (int) $minLevel) {
                    return false;
                }
            }

            // Condition: not voted today
            if (! empty($conditions['not_voted_today'])) {
                if ($this->hasVotedToday($user)) {
                    return false;
                }
            }
        }

        // Frequency check
        return $this->checkFrequency($modal, $user);
    }

    public function checkFrequency(SessionModal $modal, ?User $user): bool
    {
        $sessionKey = "session_modal_shown_{$modal->id}";

        switch ($modal->frequency) {
            case 'always':
                return true;

            case 'once_per_session':
                return ! session()->has($sessionKey);

            case 'once_per_day':
                $dayKey = $sessionKey . '_' . now()->toDateString();
                return ! session()->has($dayKey);

            case 'once_per_user':
                // Guests cannot be tracked per user — fall back to once_per_session
                if ($user === null) {
                    return ! session()->has($sessionKey);
                }
                return ! UserModalDismissal::where('user_id', $user->id)
                    ->where('session_modal_id', $modal->id)
                    ->exists();

            default:
                return true;
        }
    }

    /**
     * Record that the modal was dismissed (sets session flag and/or DB record).
     */
    public function dismiss(SessionModal $modal, ?User $user): void
    {
        $sessionKey = "session_modal_shown_{$modal->id}";

        switch ($modal->frequency) {
            case 'once_per_session':
                session()->put($sessionKey, true);
                break;

            case 'once_per_day':
                $dayKey = $sessionKey . '_' . now()->toDateString();
                session()->put($dayKey, true);
                break;

            case 'once_per_user':
                // Guests cannot be tracked per user — fall back to session-based tracking
                if ($user === null) {
                    session()->put($sessionKey, true);
                    break;
                }
                UserModalDismissal::firstOrCreate([
                    'user_id' => $user->id,
                    'session_modal_id' => $modal->id,
                ], [
                    'dismissed_at' => now(),
                ]);
                break;
        }
    }

    /**
     * Mark that a modal has been shown (for session/day tracking) without full dismissal.
     * Called when the modal is first rendered to the user.
     */
    public function markShown(SessionModal $modal): void
    {
        $sessionKey = "session_modal_shown_{$modal->id}";

        if ($modal->frequency === 'once_per_session') {
            session()->put($sessionKey, true);
        } elseif ($modal->frequency === 'once_per_day') {
            $dayKey = $sessionKey . '_' . now()->toDateString();
            session()->put($dayKey, true);
        }
    }

    private function getUserMaxCharacterLevel(User $user): int
    {
        try {
            $chars = $user->shardUsers()->get();
            return (int) $chars->max('CurLevel');
        } catch (\Throwable) {
            return 0;
        }
    }

    private function hasVotedToday(User $user): bool
    {
        $votingLogClass = 'SilkPanel\Voting\Models\VotingLog';
        if (! class_exists($votingLogClass)) {
            return false;
        }

        try {
            return $votingLogClass::where('user_id', $user->id)
                ->where('callback_received', true)
                ->whereDate('voted_at', today())
                ->exists();
        } catch (\Throwable) {
            return false;
        }
    }
}
