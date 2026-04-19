@extends('template::layouts.app')

@section('content')
    <div class="py-8">
        <div class="mx-auto max-w-[1600px] px-4 md:px-8">
            {{-- Back link --}}
            <a href="{{ route('ranking.characters') }}"
                class="mb-6 inline-flex items-center gap-1.5 text-sm gp-text-on-surface-variant hover:text-yellow-400 transition">
                <x-filament::icon icon="heroicon-o-arrow-uturn-left" class="size-4 text-yellow-400" />
                {{ __('ranking.back_to_characters') }}
            </a>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                {{-- Left: Character Info --}}
                <div class="lg:col-span-1 space-y-6">
                    {{-- Avatar Card --}}
                    <div class="gp-card gp-ornate-border p-6 text-center">
                        <img src="{{ $characterImage2d }}" alt="{{ e($character->CharName16) }}"
                            class="mx-auto h-48 w-auto object-contain mb-4" />
                        <h1 class="text-xl font-bold font-headline gp-text-on-surface uppercase tracking-widest">
                            {{ e($character->CharName16) }}
                        </h1>
                        @if ($character->guild && $character->guild->ID !== 0)
                            <a href="{{ route('ranking.guilds.show', $character->guild->ID) }}"
                                class="mt-2 inline-flex items-center gap-1 text-sm gp-text-primary hover:text-yellow-300 transition">
                                {{ e($character->guild->Name) }}
                            </a>
                        @endif
                    </div>

                    {{-- Stats Card --}}
                    <div class="gp-card gp-ornate-border p-6">
                        <h2 class="mb-4 text-xs font-bold font-headline uppercase tracking-widest text-yellow-600">
                            {{ __('ranking.character_stats') }}
                        </h2>
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm gp-text-on-surface-variant">{{ __('ranking.level') }}</dt>
                                <dd class="text-sm font-bold gp-text-on-surface">{{ $character->CurLevel }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm gp-text-on-surface-variant">{{ __('ranking.strength') }}</dt>
                                <dd class="text-sm font-bold gp-text-on-surface">{{ number_format($character->Strength) }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm gp-text-on-surface-variant">{{ __('ranking.intellect') }}</dt>
                                <dd class="text-sm font-bold gp-text-on-surface">{{ number_format($character->Intellect) }}
                                </dd>
                            </div>
                            <div class="pt-3 flex justify-between" style="border-top: 1px solid var(--gp-outline-variant);">
                                <dt class="text-sm gp-text-on-surface-variant">HP</dt>
                                <dd class="text-sm font-bold gp-text-secondary">{{ number_format($character->HP) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm gp-text-on-surface-variant">MP</dt>
                                <dd class="text-sm font-bold gp-text-tertiary">{{ number_format($character->MP) }}</dd>
                            </div>
                            <div class="pt-3 flex justify-between" style="border-top: 1px solid var(--gp-outline-variant);">
                                <dt class="text-sm gp-text-on-surface-variant">{{ __('ranking.experience') }}</dt>
                                <dd class="text-sm font-bold gp-text-primary">
                                    {{ $character->getCharRefLevelExperience() }}%</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Right: Equipment & Avatar --}}
                <div class="lg:col-span-3" x-data="{ tab: 'equipment' }">
                    <div class="gp-card gp-ornate-border p-6">
                        {{-- Tab Switch --}}
                        <div class="mb-4 flex items-center gap-2">
                            <button @click="tab = 'equipment'" type="button"
                                :class="tab === 'equipment'
                                    ?
                                    'gp-gold-btn shadow-lg' :
                                    'gp-card-low gp-text-on-surface-variant hover:text-yellow-400 hover:bg-yellow-900/10 cursor-pointer'"
                                class="px-4 py-1.5 text-sm font-bold font-headline uppercase tracking-widest transition">
                                {{ __('ranking.equipment') }}
                            </button>
                            @if ($avatar->isNotEmpty())
                                <button @click="tab = 'avatar'" type="button"
                                    :class="tab === 'avatar'
                                        ?
                                        'gp-gold-btn shadow-lg' :
                                        'gp-card-low gp-text-on-surface-variant hover:text-yellow-400 hover:bg-yellow-900/10 cursor-pointer'"
                                    class="px-4 py-1.5 text-sm font-bold font-headline uppercase tracking-widest transition">
                                    {{ __('ranking.avatar') }}
                                </button>
                            @endif
                        </div>

                        {{-- Equipment Tab --}}
                        <div x-show="tab === 'equipment'" x-transition:enter.opacity.duration.200ms>
                            @include('template::ranking.partials.equipment', [
                                'equipment' => $equipment,
                                'characterImage2d' => $characterFullImage2d,
                            ])
                        </div>

                        {{-- Avatar Tab --}}
                        @if ($avatar->isNotEmpty())
                            <div x-show="tab === 'avatar'" x-cloak x-transition:enter.opacity.duration.200ms>
                                @include('template::ranking.partials.avatar', [
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
