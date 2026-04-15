<?php

namespace App\Livewire;

use App\Services\EventTimerService;
use Livewire\Component;

class EventTimers extends Component
{
    public function render()
    {
        return view('template::livewire.event-timers', [
            'timers' => EventTimerService::getTimersWithCountdown(),
        ]);
    }
}
