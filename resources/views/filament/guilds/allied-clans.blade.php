<div>
    @forelse ($record->AlliedGuilds as $alliedGuild)
        <div class="flex">
            @if ($alliedGuild['is_current_guild'] == $record->ID)
                <div class="guild-master-row-marker"></div>
            @else
                <x-filament::link :href="route('filament.admin.resources.guilds.view', ['record' => $alliedGuild['guild_id']])" target="_blank" icon="heroicon-o-arrow-top-right-on-square"
                    iconSize="sm" icon-position="after">
                    {{ $alliedGuild['guild_name'] }}
                </x-filament::link>
            @endif
        </div>
    @empty
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ __('filament/guilds.allied_clans.empty') }}
        </p>
    @endforelse
</div>
