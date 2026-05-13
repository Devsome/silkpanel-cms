@if (!empty($discordId) || !empty($inviteUrl))
    <div class="bg-zinc-900 border border-violet-500/20 p-5">
        <div class="mb-4 flex items-center justify-between">
            <p class="text-xs font-mono uppercase tracking-[0.25em] text-violet-400/70">
                {{ __('index.discord.title') }}
            </p>
            @if (!empty($inviteUrl))
                <a href="{{ e($inviteUrl) }}" target="_blank" rel="noopener noreferrer"
                    class="text-xs font-mono uppercase tracking-wider text-fuchsia-400 hover:text-fuchsia-300 transition">
                    {{ __('index.discord.join') }} →
                </a>
            @endif
        </div>

        <div class="border border-zinc-800 overflow-hidden">
            @if (!empty($discordId))
                <iframe src="https://discord.com/widget?id={{ urlencode($discordId) }}&theme=dark" width="100%"
                    height="320" allowtransparency="true" frameborder="0"
                    sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts"
                    title="Discord Widget"></iframe>
            @else
                <div class="px-4 py-8 text-center text-xs font-mono uppercase tracking-[0.3em] text-zinc-600">
                    {{ __('index.discord.not_set') }}
                </div>
            @endif
        </div>
    </div>
@endif
