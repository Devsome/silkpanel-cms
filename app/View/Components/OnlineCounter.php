<?php

namespace App\View\Components;

use App\Services\FakePlayerService;
use App\Services\OnlinePlayerCountService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class OnlineCounter extends Component
{
    const CACHE_KEY = 'online_counter';

    const CACHE_TTL = 60;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $count = $this->publicCount();

        return view('template::components.online-counter', ['onlineCount' => $count]);
    }

    public static function getData(): int
    {
        return (new self)->publicCount();
    }

    /**
     * The player count shown publicly: the real count augmented with a stable
     * fake offset when the feature is enabled. The real cached count remains
     * untouched.
     */
    protected function publicCount(): int
    {
        $real = $this->updateOnlineCountCache();

        return app(FakePlayerService::class)->augment($real);
    }

    protected function updateOnlineCountCache(): int
    {
        if (cache()->has(self::CACHE_KEY)) {
            return cache()->get(self::CACHE_KEY);
        }

        $count = $this->fetchOnlineCount();
        cache()->put(self::CACHE_KEY, $count, self::CACHE_TTL);

        return $count;
    }

    /**
     * The real count, from the configured custom source when the backend
     * override is active, otherwise from `_ShardCurrentUser`.
     */
    protected function fetchOnlineCount(): int
    {
        return app(OnlinePlayerCountService::class)->count();
    }
}
