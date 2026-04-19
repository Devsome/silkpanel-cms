@if (!empty($discordId) || !empty($inviteUrl))
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                {{ __('index.discord.title') }}
            </h3>
            @if (!empty($inviteUrl))
                <a href="{{ e($inviteUrl) }}" target="_blank" rel="noopener noreferrer"
                    class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                    {{ __('index.discord.join') }}
                </a>
            @endif
        </div>

        <div class="rounded-lg overflow-hidden border border-indigo-500/30 bg-gray-900/80">
            @if (!empty($discordId))
                <iframe src="https://discord.com/widget?id={{ urlencode($discordId) }}&theme=dark" width="100%"
                    height="320" allowtransparency="true" frameborder="0"
                    sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts"
                    title="Discord Widget"></iframe>
            @else
                <div class="px-4 py-8 text-center text-sm text-gray-300">
                    {{ __('index.discord.not_set') }}
                </div>
            @endif
        </div>
    </div>
@endif
