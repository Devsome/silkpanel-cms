@extends('template::layouts.app', ['title' => e($guild->Name)])

@section('content')
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Back link --}}
            <a href="{{ route('ranking.guilds') }}"
                class="inline-flex items-center gap-1.5 text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 mb-6 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('ranking.back_to_guilds') }}
            </a>

            {{-- Guild Header --}}
            <div
                class="bg-gradient-to-r from-indigo-500/10 to-purple-500/10 dark:from-indigo-500/20 dark:to-purple-500/20 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ e($guild->Name) }}
                        </h1>
                        <div class="mt-3 flex flex-wrap gap-3">
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                {{ __('ranking.guild_level') }}: {{ $guild->Lvl }}
                            </span>
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $members->count() }} {{ __('ranking.members') }}
                            </span>
                            @if ($guild->FoundationDate)
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider">
                        {{ __('ranking.guild_members') }}
                    </h2>
                </div>
                @if ($members->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800/50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        #</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('ranking.character_name') }}</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('ranking.nickname') }}</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('ranking.join_date') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($members as $member)
                                    <tr
                                        class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors {{ (int) $member->MemberClass === 0 ? 'bg-amber-50/50 dark:bg-amber-900/10' : '' }}">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                            <a href="{{ route('ranking.characters.show', $member->CharID) }}"
                                                class="inline-flex items-center gap-1 font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                                @if ((int) $member->MemberClass === 0)
                                                    <svg class="w-4 h-4 text-amber-500" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @endif
                                                {{ e($member->CharName) }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ e($member->Nickname ?: '—') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
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
                    <div class="text-center py-12">
                        <p class="text-gray-500 dark:text-gray-400">{{ __('ranking.no_members') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
