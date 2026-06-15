<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Http;
use Livewire\Component;

class SaasMessages extends Component
{
    public array $notifications = [];
    public array $surveys = [];

    public ?array $activeSurvey = null;
    public string $surveyResponse = '';

    public function mount(): void
    {
        $this->fetchMessages();
    }

    public function fetchMessages(): void
    {
        try {
            $response = Http::timeout(5)
                ->withHeader('X-SILKPANEL', config('silkpanel.api_key'))
                ->get(config('silkpanel.saas_url') . '/api/sp/messages/pending');

            if (! $response->successful()) {
                return;
            }

            $messages = $response->json('messages', []);

            $this->notifications = array_values(array_filter($messages, fn($m) => $m['type'] === 'notification'));
            $this->surveys       = array_values(array_filter($messages, fn($m) => $m['type'] === 'survey'));

            if (empty($this->activeSurvey) && ! empty($this->surveys)) {
                $this->activeSurvey = $this->surveys[0];
            }
        } catch (\Throwable) {
            // Silently ignore — SaaS may not be reachable
        }
    }

    public function dismissNotification(int $id): void
    {
        try {
            Http::timeout(5)
                ->withHeader('X-SILKPANEL', config('silkpanel.api_key'))
                ->post(config('silkpanel.saas_url') . "/api/sp/messages/{$id}/read");
        } catch (\Throwable) {
        }

        $this->notifications = array_values(
            array_filter($this->notifications, fn($n) => $n['id'] !== $id)
        );
    }

    public function submitSurvey(): void
    {
        if (! $this->activeSurvey || trim($this->surveyResponse) === '') {
            return;
        }

        try {
            Http::timeout(5)
                ->withHeader('X-SILKPANEL', config('silkpanel.api_key'))
                ->post(config('silkpanel.saas_url') . "/api/sp/messages/{$this->activeSurvey['id']}/answer", [
                    'response' => $this->surveyResponse,
                ]);
        } catch (\Throwable) {
        }

        $submittedId     = $this->activeSurvey['id'];
        $this->surveys   = array_values(array_filter($this->surveys, fn($s) => $s['id'] !== $submittedId));
        $this->activeSurvey   = ! empty($this->surveys) ? $this->surveys[0] : null;
        $this->surveyResponse = '';
    }

    public function render()
    {
        return view('livewire.saas-messages');
    }
}
