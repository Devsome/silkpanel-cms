@if (!empty($discordId) || !empty($inviteUrl))
    <div class="gp-card gp-ornate-border p-6 shadow-2xl">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="font-headline text-xs font-bold uppercase tracking-widest gp-text-primary">
                {{ __('index.discord.title') }}
            </h3>
            @if (!empty($inviteUrl))
                <a href="{{ e($inviteUrl) }}" target="_blank" rel="noopener noreferrer"
                    class="text-xs font-semibold uppercase tracking-wider gp-text-primary hover:text-yellow-300 transition-colors">
                    {{ __('index.discord.join') }}
                </a>
            @endif
        </div>

        <div class="gp-card-lowest gp-ghost-border overflow-hidden">
            @if (!empty($discordId))
                <iframe src="https://discord.com/widget?id={{ urlencode($discordId) }}&theme=dark" width="100%"
                    height="320" allowtransparency="true" frameborder="0"
                    sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts"
                    title="Discord Widget"></iframe>
            @else
                <div class="px-4 py-8 text-center text-sm gp-text-on-surface-variant">
                    {{ __('index.discord.not_set') }}
                </div>
            @endif
        </div>
    </div>
@endif
