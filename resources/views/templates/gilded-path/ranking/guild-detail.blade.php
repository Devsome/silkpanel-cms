@extends('template::layouts.app')

@section('content')
    <div class="py-8">
        <div class="mx-auto max-w-[1600px] px-4 md:px-8">
            {{-- Back link --}}
            <a href="{{ route('ranking.guilds') }}"
                class="mb-6 inline-flex items-center gap-1.5 text-sm gp-text-on-surface-variant hover:text-yellow-400 transition">
                <x-filament::icon icon="heroicon-o-arrow-uturn-left" class="size-4 text-yellow-400" />
                {{ __('ranking.back_to_guilds') }}
            </a>

            {{-- Guild Header --}}
            <div class="gp-card gp-ornate-border p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold font-headline gp-text-primary uppercase tracking-widest">
                            {{ e($guild->Name) }}
                        </h1>
                        <div class="mt-2 flex flex-wrap gap-4 text-sm gp-text-on-surface-variant">
                            <span class="inline-flex items-center gap-1.5">
                                {{ __('ranking.guild_level') }}: <span
                                    class="gp-text-on-surface font-semibold">{{ $guild->Lvl }}</span>
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <x-filament::icon icon="heroicon-o-users" class="size-4 gp-text-tertiary" />
                                <span class="gp-text-on-surface font-semibold">{{ $members->count() }}</span>
                                {{ __('ranking.members') }}
                            </span>
                            @if ($guild->FoundationDate)
                                <span class="inline-flex items-center gap-1.5">
                                    <x-filament::icon icon="heroicon-o-calendar" class="size-4 gp-text-secondary" />
                                    {{ \Carbon\Carbon::parse($guild->FoundationDate)->format('d M Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Members Table --}}
            <div class="gp-card overflow-hidden">
                <div class="px-6 py-4" style="border-bottom: 1px solid var(--gp-outline-variant);">
                    <h2 class="text-xs font-bold font-headline uppercase tracking-widest text-yellow-600">
                        {{ __('ranking.guild_members') }}
                    </h2>
                </div>
                @if ($members->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y" style="border-color: var(--gp-outline-variant);">
                            <thead style="background-color: var(--gp-surface-container-lowest);">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider gp-text-outline">
                                        #
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider gp-text-outline">
                                        {{ __('ranking.character_name') }}
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider gp-text-outline">
                                        {{ __('ranking.nickname') }}
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider gp-text-outline">
                                        {{ __('ranking.join_date') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($members as $member)
                                    <tr class="transition-colors hover:bg-yellow-900/5 {{ (int) $member->MemberClass === 0 ? 'bg-yellow-900/10' : '' }}"
                                        style="border-bottom: 1px solid rgba(77,70,53,0.1);">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm gp-text-outline">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                            <a href="{{ route('ranking.characters.show', $member->CharID) }}"
                                                class="inline-flex items-center gap-1.5 font-medium gp-text-primary hover:text-yellow-300 transition">
                                                @if ((int) $member->MemberClass === 0)
                                                    <x-filament::icon icon="heroicon-m-star"
                                                        class="size-4 text-yellow-500" />
                                                @endif
                                                {{ e($member->CharName) }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm gp-text-outline">
                                            {{ e($member->Nickname ?: '—') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm gp-text-outline">
                                            @if ($member->JoinDate)
                                                {{ \Carbon\Carbon::parse($member->JoinDate)->format('d M Y, H:i') }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="py-12 text-center">
                        <p class="gp-text-outline">{{ __('ranking.no_members') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
