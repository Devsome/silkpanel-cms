<table class="relative min-w-full divide-y divide-gray-300 dark:divide-white/15">
    <tbody class="divide-y divide-gray-200 bg-white dark:divide-white/10 dark:bg-gray-900">
        <tr>
            <td class="py-5 pr-3 pl-4 text-sm whitespace-nowrap sm:pl-0">
                <div class="flex items-center">
                    <div class="size-4 shrink-0">
                        <img src="{{ asset('images/silkroad/jobs/trader.png') }}" alt="Trader Job Icon"
                            class="size-4 dark:outline dark:outline-white/10" />
                    </div>
                    <div class="ml-4">
                        <div class="font-medium text-gray-900 dark:text-white">
                            {{ __('filament/characters.view.joblvl_trader') }}: {{ $record->JobLvl_Trader }}
                        </div>
                    </div>
                </div>
            </td>
            <td class="px-3 py-5 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                <div class="text-gray-900 dark:text-white">
                    {{ __('filament/characters.view.trader_exp') }}: {{ $record->Trader_Exp }}
                </div>
            </td>
        </tr>
        <tr>
            <td class="py-5 pr-3 pl-4 text-sm whitespace-nowrap sm:pl-0">
                <div class="flex items-center">
                    <div class="size-4 shrink-0">
                        <img src="{{ asset('images/silkroad/jobs/hunter.png') }}" alt="Hunter Job Icon"
                            class="size-4 dark:outline dark:outline-white/10" />
                    </div>
                    <div class="ml-4">
                        <div class="font-medium text-gray-900 dark:text-white">
                            {{ __('filament/characters.view.joblvl_hunter') }}: {{ $record->JobLvl_Hunter }}
                        </div>
                    </div>
                </div>
            </td>
            <td class="px-3 py-5 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                <div class="text-gray-900 dark:text-white">
                    {{ __('filament/characters.view.hunter_exp') }}: {{ $record->Hunter_Exp }}
                </div>
            </td>
        </tr>
        <tr>
            <td class="py-5 pr-3 pl-4 text-sm whitespace-nowrap sm:pl-0">
                <div class="flex items-center">
                    <div class="size-4 shrink-0">
                        <img src="{{ asset('images/silkroad/jobs/robber.png') }}" alt="Robber Job Icon"
                            class="size-4 dark:outline dark:outline-white/10" />
                    </div>
                    <div class="ml-4">
                        <div class="font-medium text-gray-900 dark:text-white">
                            {{ __('filament/characters.view.joblvl_robber') }}: {{ $record->JobLvl_Robber }}
                        </div>
                    </div>
                </div>
            </td>
            <td class="px-3 py-5 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                <div class="text-gray-900 dark:text-white">
                    {{ __('filament/characters.view.robber_exp') }}: {{ $record->Robber_Exp }}
                </div>
            </td>
        </tr>
    </tbody>
</table>
