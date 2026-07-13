<?php

namespace App\View\Components;

use App\Services\GlobalsService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CharacterGlobals extends Component
{
    public string $name;

    public int $limit;

    public function __construct(string $name, int $limit = 10)
    {
        $this->name = $name;
        $this->limit = max(1, min($limit, 50));
    }

    public function render(): View|Closure|string
    {
        // Only iSRO exposes a standard global message log; the panel is hidden
        // entirely on other versions (see GlobalsService for the future
        // custom-source extension point).
        $available = config('silkpanel.version') === 'isro';

        $globals = $available
            ? app(GlobalsService::class)->forCharacter($this->name, $this->limit)
            : collect();

        return view('template::components.character-globals', [
            'globals' => $globals,
            'available' => $available,
        ]);
    }
}
