@if (!empty($discordId) || !empty($inviteUrl))
    <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-6 shadow-2xl">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-xs font-bold uppercase tracking-widest text-emerald-400/70">
                {{ __('index.discord.title') }}
            </h3>
            @if (!empty($inviteUrl))
                <a href="{{ e($inviteUrl) }}" target="_blank" rel="noopener noreferrer"
                    class="text-xs font-semibold text-emerald-400 hover:text-emerald-300 transition-colors">
                    {{ __('index.discord.join') }}
                </a>
            @endif
        </div>

        <div class="rounded-xl overflow-hidden border border-gray-800">
            @if (!empty($discordId))
                <iframe src="https://discord.com/widget?id={{ urlencode($discordId) }}&theme=dark" width="100%"
                    height="320" allowtransparency="true" frameborder="0"
                    sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts"
                    title="Discord Widget"></iframe>
            @else
                <div class="px-4 py-8 text-center text-sm text-gray-500">
                    {{ __('index.discord.not_set') }}
                </div>
            @endif
        </div>
    </div>
@endif
