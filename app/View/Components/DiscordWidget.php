<?php

namespace App\View\Components;

use App\Helpers\SettingHelper;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DiscordWidget extends Component
{
    public function render(): View|Closure|string
    {
        $discordId = (string) SettingHelper::get('discord_id', '');
        $inviteUrl = (string) SettingHelper::get('social_discord', '');

        return view('template::components.discord-widget', [
            'discordId' => trim($discordId),
            'inviteUrl' => trim($inviteUrl),
        ]);
    }
}
