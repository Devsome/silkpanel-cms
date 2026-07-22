<?php

namespace App\Filament\Concerns;

/**
 * Adds a reusable "locked" state to a Filament page.
 *
 * A locked page stays visible in the navigation but its content is rendered
 * behind a blurred, non-interactive overlay (see the <x-locked-overlay>
 * Blade component). Override {@see isLocked()} to gate a page, e.g. behind a
 * valid license, and pair it with disabled form inputs plus a server-side
 * guard on every write action so the lock cannot be bypassed.
 */
trait InteractsWithLockedState
{
    /**
     * Whether this page is locked (visible but not usable).
     */
    public function isLocked(): bool
    {
        return false;
    }

    /**
     * Heading shown on the lock overlay.
     */
    public function getLockedTitle(): string
    {
        return __('filament/settings.locked.title');
    }

    /**
     * Explanatory text shown on the lock overlay.
     */
    public function getLockedDescription(): string
    {
        return __('filament/settings.locked.description');
    }

    /**
     * Icon shown on the lock overlay.
     */
    public function getLockedIcon(): string
    {
        return 'heroicon-o-lock-closed';
    }
}
