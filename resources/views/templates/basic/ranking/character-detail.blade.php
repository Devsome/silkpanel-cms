@extends('template::layouts.app', ['title' => e($character->CharName16)])

@section('content')
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Back link --}}
            <a href="{{ route('ranking.characters') }}"
                class="inline-flex items-center gap-1.5 text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 mb-6 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('ranking.back_to_characters') }}
            </a>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                {{-- Left: Character Info --}}
                <div class="lg:col-span-1 space-y-6">
                    {{-- Avatar Card --}}
                    <div
                        class="bg-gradient-to-b from-indigo-500/10 to-transparent dark:from-indigo-500/20 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 text-center">
                        <img src="{{ $characterImage2d }}" alt="{{ e($character->CharName16) }}"
                            class="h-48 w-auto mx-auto object-contain mb-4" />
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                            {{ e($character->CharName16) }}
                        </h1>
                        @if ($character->guild && $character->guild->ID !== 0)
                            <a href="{{ route('ranking.guilds.show', $character->guild->ID) }}"
                                class="inline-flex items-center gap-1 mt-2 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                {{ e($character->guild->Name) }}
                            </a>
                        @endif
                    </div>

                    {{-- Stats Card --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4">
                            {{ __('ranking.character_stats') }}
                        </h2>
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('ranking.level') }}</dt>
                                <dd class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $character->CurLevel }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('ranking.strength') }}</dt>
                                <dd class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($character->Strength) }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('ranking.intellect') }}</dt>
                                <dd class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($character->Intellect) }}
                                </dd>
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-3 flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">HP</dt>
                                <dd class="text-sm font-semibold text-red-600 dark:text-red-400">
                                    {{ number_format($character->HP) }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">MP</dt>
                                <dd class="text-sm font-semibold text-blue-600 dark:text-blue-400">
                                    {{ number_format($character->MP) }}
                                </dd>
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-3 flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('ranking.experience') }}</dt>
                                <dd class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $character->getCharRefLevelExperience() }}%
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Right: Equipment & Avatar --}}
                <div class="lg:col-span-3" x-data="{ tab: 'equipment' }">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
                        {{-- Tab Switch --}}
                        <div class="flex items-center gap-2 mb-4">
                            <button @click="tab = 'equipment'" type="button"
                                :class="tab === 'equipment'
                                    ?
                                    'bg-indigo-600 text-white shadow-sm' :
                                    'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 cursor-pointer'"
                                class="px-4 py-1.5 rounded-full text-sm font-semibold transition">
                                {{ __('ranking.equipment') }}
                            </button>
                            @if ($avatar->isNotEmpty())
                                <button @click="tab = 'avatar'" type="button"
                                    :class="tab === 'avatar'
                                        ?
                                        'bg-indigo-600 text-white shadow-sm' :
                                        'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 cursor-pointer'"
                                    class="px-4 py-1.5 rounded-full text-sm font-semibold transition">
                                    {{ __('ranking.avatar') }}
                                </button>
                            @endif
                        </div>

                        {{-- Equipment Tab --}}
                        <div x-show="tab === 'equipment'" x-transition:enter.opacity.duration.200ms>
                            @include('ranking.partials.equipment', [
                                'equipment' => $equipment,
                                'characterImage2d' => $characterFullImage2d,
                            ])
                        </div>

                        {{-- Avatar Tab --}}
                        @if ($avatar->isNotEmpty())
                            <div x-show="tab === 'avatar'" x-cloak x-transition:enter.opacity.duration.200ms>
                                @include('ranking.partials.avatar', [
                                    'avatar' => $avatar,
                                    'characterImage2d' => $characterFullImage2d,
                                ])
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
