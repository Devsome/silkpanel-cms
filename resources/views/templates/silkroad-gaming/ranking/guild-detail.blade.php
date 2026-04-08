@extends('template::layouts.app')

@section('content')
    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            {{-- Back link --}}
            <a href="{{ route('ranking.guilds') }}"
                class="mb-6 inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-emerald-400 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('ranking.back_to_guilds') }}
            </a>

            {{-- Guild Header --}}
            <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-white">
                            {{ e($guild->Name) }}
                        </h1>
                        <div class="mt-2 flex flex-wrap gap-4 text-sm text-gray-400">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                {{ __('ranking.guild_level') }}: <span
                                    class="text-white font-semibold">{{ $guild->Lvl }}</span>
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="text-white font-semibold">{{ $members->count() }}</span>
                                {{ __('ranking.members') }}
                            </span>
                            @if ($guild->FoundationDate)
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ \Carbon\Carbon::parse($guild->FoundationDate)->format('d M Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Members Table --}}
            <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur overflow-hidden">
                <div class="border-b border-gray-800 px-6 py-4">
                    <h2 class="text-xs font-bold uppercase tracking-widest text-emerald-400/70">
                        {{ __('ranking.guild_members') }}
                    </h2>
                </div>
                @if ($members->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-800">
                            <thead class="bg-gray-900/80">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        #
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        {{ __('ranking.character_name') }}
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        {{ __('ranking.nickname') }}
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        {{ __('ranking.join_date') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800/50">
                                @foreach ($members as $member)
                                    <tr
                                        class="transition-colors hover:bg-emerald-500/5 {{ (int) $member->MemberClass === 0 ? 'bg-amber-500/5' : '' }}">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                            <a href="{{ route('ranking.characters.show', $member->CharID) }}"
                                                class="inline-flex items-center gap-1.5 font-medium text-emerald-400 hover:text-emerald-300 transition">
                                                @if ((int) $member->MemberClass === 0)
                                                    <svg class="w-4 h-4 text-amber-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @endif
                                                {{ e($member->CharName) }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                            {{ e($member->Nickname ?: '—') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
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
                        <p class="text-gray-500">{{ __('ranking.no_members') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
