@extends('template::layouts.app')

@section('content')
    <div class="py-8">
        <div class="mx-auto max-w-[1600px] px-4 md:px-8">
            {{-- Back link --}}
            <a href="{{ route('ranking.guilds') }}"
                class="mb-6 inline-flex items-center gap-2 text-xs ag-font-display font-semibold tracking-wider uppercase ag-text-muted hover:ag-text-primary transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('ranking.back_to_guilds') }}
            </a>

            {{-- Guild Header --}}
            <div class="ag-card-glow p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold ag-font-display ag-text-primary uppercase tracking-widest">
                            {{ e($guild->Name) }}
                        </h1>
                        <div class="mt-2 flex flex-wrap gap-4 text-sm ag-text-muted">
                            <span class="inline-flex items-center gap-1.5">
                                {{ __('ranking.guild_level') }}: <span class="ag-text-surface font-semibold">{{ $guild->Lvl }}</span>
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="h-4 w-4 ag-text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="ag-text-surface font-semibold">{{ $members->count() }}</span>
                                {{ __('ranking.members') }}
                            </span>
                            @if ($guild->FoundationDate)
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="h-4 w-4 ag-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ \Carbon\Carbon::parse($guild->FoundationDate)->format('d M Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Members Table --}}
            <div class="ag-card overflow-hidden">
                <div class="px-6 py-4 border-b ag-divider">
                    <h2 class="text-xs font-bold ag-font-display uppercase tracking-widest ag-text-secondary">
                        {{ __('ranking.guild_members') }}
                    </h2>
                </div>
                @if ($members->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="ag-table min-w-full">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('ranking.character_name') }}</th>
                                    <th>{{ __('ranking.nickname') }}</th>
                                    <th>{{ __('ranking.join_date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($members as $member)
                                    <tr class="{{ (int) $member->MemberClass === 0 ? 'bg-cyan-900/10' : '' }}">
                                        <td class="ag-text-muted">{{ $loop->iteration }}</td>
                                        <td>
                                            <a href="{{ route('ranking.characters.show', $member->slug) }}"
                                                class="inline-flex items-center gap-1.5 font-medium ag-text-primary hover:opacity-80 transition">
                                                @if ((int) $member->MemberClass === 0)
                                                    <svg class="h-4 w-4 ag-stat-amber" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                    </svg>
                                                @endif
                                                {{ e($member->CharName) }}
                                            </a>
                                        </td>
                                        <td class="ag-text-muted">{{ e($member->Nickname ?: '—') }}</td>
                                        <td class="ag-text-muted">
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
                        <p class="ag-text-muted">{{ __('ranking.no_members') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
