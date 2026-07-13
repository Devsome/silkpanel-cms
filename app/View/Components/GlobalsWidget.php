<?php

namespace App\View\Components;

use App\Services\GlobalsService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class GlobalsWidget extends Component
{
    public int $limit;

    public function __construct(int $limit = 10)
    {
        $this->limit = max(1, min($limit, 50));
    }

    public function render(): View|Closure|string
    {
        // Only iSRO exposes a standard global message log. On every other
        // version the widget renders nothing (see GlobalsService for the
        // future custom-source extension point).
        $available = config('silkpanel.version') === 'isro';

        $globals = $available
            ? app(GlobalsService::class)->latest($this->limit)
            : collect();

        return view('template::components.globals-widget', [
            'globals' => $globals,
            'available' => $available,
        ]);
    }
}
