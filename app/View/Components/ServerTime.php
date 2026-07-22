<?php

namespace App\View\Components;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Live-ticking current server time, rendered in the CMS timezone
 * (config('app.timezone'), overridable via the `server_timezone` setting
 * or a `timezone` prop). Template-agnostic: the view resolves through the
 * `template::` namespace and falls back to the app-level component view,
 * so every template gets it for free and may override the markup.
 *
 * Usage:
 *   <x-server-time class="ds-stat" />
 *   @serverTime
 */
class ServerTime extends Component
{
    public function __construct(
        public ?string $timezone = null,
        public string $format = 'H:i:s',
    ) {
        //
    }

    public function render(): View|Closure|string
    {
        $tz = $this->timezone
            ?: (\App\Helpers\SettingHelper::get('server_timezone') ?: config('app.timezone', 'UTC'));

        // Guard against an invalid stored timezone.
        try {
            $now = Carbon::now($tz);
        } catch (\Throwable) {
            $tz = config('app.timezone', 'UTC');
            $now = Carbon::now($tz);
        }

        return view('template::components.server-time', [
            'timezone' => $tz,
            'initial' => $now->format($this->format),
            'epochMs' => $now->getTimestampMs(),
        ]);
    }

    /**
     * Convenience accessor for the @serverTime directive / other callers.
     */
    public static function getData(?string $timezone = null, string $format = 'H:i:s'): string
    {
        $tz = $timezone
            ?: (\App\Helpers\SettingHelper::get('server_timezone') ?: config('app.timezone', 'UTC'));

        try {
            return Carbon::now($tz)->format($format);
        } catch (\Throwable) {
            return Carbon::now(config('app.timezone', 'UTC'))->format($format);
        }
    }
}
