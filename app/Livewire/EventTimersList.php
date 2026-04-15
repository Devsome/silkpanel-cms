<?php

namespace App\Livewire;

use App\Services\EventTimerService;
use Livewire\Component;

class EventTimersList extends Component
{
    public function render()
    {
        return view('template::livewire.event-timers-list', [
            'timers' => EventTimerService::getTimersWithCountdown(),
        ]);
    }
}
