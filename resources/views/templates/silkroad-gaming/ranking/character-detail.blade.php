@extends('template::layouts.app')

@section('content')
    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            {{-- Back link --}}
            <a href="{{ route('ranking.characters') }}"
                class="mb-6 inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-emerald-400 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('ranking.back_to_characters') }}
            </a>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                {{-- Left: Character Info --}}
                <div class="lg:col-span-1 space-y-6">
                    {{-- Avatar Card --}}
                    <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-6 text-center">
                        <img src="{{ $characterImage2d }}" alt="{{ e($character->CharName16) }}"
                            class="mx-auto h-48 w-auto object-contain mb-4" />
                        <h1 class="text-xl font-bold text-white">
                            {{ e($character->CharName16) }}
                        </h1>
                        @if ($character->guild && $character->guild->ID !== 0)
                            <a href="{{ route('ranking.guilds.show', $character->guild->ID) }}"
                                class="mt-2 inline-flex items-center gap-1 text-sm text-emerald-400 hover:text-emerald-300 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                {{ e($character->guild->Name) }}
                            </a>
                        @endif
                    </div>

                    {{-- Stats Card --}}
                    <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-6">
                        <h2 class="mb-4 text-xs font-bold uppercase tracking-widest text-emerald-400/70">
                            {{ __('ranking.character_stats') }}
                        </h2>
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-400">{{ __('ranking.level') }}</dt>
                                <dd class="text-sm font-bold text-white">{{ $character->CurLevel }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-400">{{ __('ranking.strength') }}</dt>
                                <dd class="text-sm font-bold text-white">{{ number_format($character->Strength) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-400">{{ __('ranking.intellect') }}</dt>
                                <dd class="text-sm font-bold text-white">{{ number_format($character->Intellect) }}</dd>
                            </div>
                            <div class="border-t border-gray-800 pt-3 flex justify-between">
                                <dt class="text-sm text-gray-400">HP</dt>
                                <dd class="text-sm font-bold text-red-400">{{ number_format($character->HP) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-400">MP</dt>
                                <dd class="text-sm font-bold text-cyan-400">{{ number_format($character->MP) }}</dd>
                            </div>
                            <div class="border-t border-gray-800 pt-3 flex justify-between">
                                <dt class="text-sm text-gray-400">{{ __('ranking.experience') }}</dt>
                                <dd class="text-sm font-bold text-emerald-400">
                                    {{ $character->getCharRefLevelExperience() }}%</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Right: Equipment & Avatar --}}
                <div class="lg:col-span-3" x-data="{ tab: 'equipment' }">
                    <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-6">
                        {{-- Tab Switch --}}
                        <div class="mb-4 flex items-center gap-2">
                            <button @click="tab = 'equipment'" type="button"
                                :class="tab === 'equipment'
                                    ?
                                    'bg-linear-to-r from-emerald-500 to-cyan-500 text-gray-950 shadow-lg shadow-emerald-500/20' :
                                    'bg-gray-800/50 text-gray-400 hover:text-white hover:bg-gray-800 cursor-pointer'"
                                class="px-4 py-1.5 rounded-xl text-sm font-semibold transition">
                                {{ __('ranking.equipment') }}
                            </button>
                            @if ($avatar->isNotEmpty())
                                <button @click="tab = 'avatar'" type="button"
                                    :class="tab === 'avatar'
                                        ?
                                        'bg-linear-to-r from-emerald-500 to-cyan-500 text-gray-950 shadow-lg shadow-emerald-500/20' :
                                        'bg-gray-800/50 text-gray-400 hover:text-white hover:bg-gray-800 cursor-pointer'"
                                    class="px-4 py-1.5 rounded-xl text-sm font-semibold transition">
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
