@if (!empty($discordId) || !empty($inviteUrl))
    <div class="ag-card p-6 shadow-2xl">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="ag-font-display text-xs font-bold uppercase tracking-widest ag-text-primary">
                {{ __('index.discord.title') }}
            </h3>
            @if (!empty($inviteUrl))
                <a href="{{ e($inviteUrl) }}" target="_blank" rel="noopener noreferrer"
                    class="text-xs font-semibold uppercase tracking-wider ag-text-primary hover:opacity-80 transition-colors">
                    {{ __('index.discord.join') }}
                </a>
            @endif
        </div>

        <div class="overflow-hidden" style="border:1px solid rgba(34,211,238,0.1);background:rgba(13,18,36,0.8);">
            @if (!empty($discordId))
                <iframe src="https://discord.com/widget?id={{ urlencode($discordId) }}&theme=dark" width="100%"
                    height="320" allowtransparency="true" frameborder="0"
                    sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts"
                    title="Discord Widget"></iframe>
            @else
                <div class="px-4 py-8 text-center text-sm ag-text-muted">
                    {{ __('index.discord.not_set') }}
                </div>
            @endif
        </div>
    </div>
@endif
