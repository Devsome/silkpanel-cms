@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="mb-6">
                <a href="{{ route('ranking.guilds') }}"
                    class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-wider text-zinc-600 hover:text-violet-400 transition">
                    ← {{ __('ranking.back_to_guilds') }}
                </a>
            </div>

            {{-- Guild Header --}}
            <div class="bg-zinc-900 border border-violet-500/20 p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center gap-6">
                    @if ($guild->CrestDataUri)
                        <img src="{{ $guild->CrestDataUri }}" alt="{{ e($guild->Name) }} crest"
                            class="w-16 h-16 object-contain border border-zinc-700 bg-zinc-950">
                    @endif
                    <div>
                        <p class="text-xs font-mono uppercase tracking-[0.25em] text-violet-400/70 mb-1">
                            {{ __('ranking.guild') }}</p>
                        <h1 class="text-2xl font-black uppercase tracking-wider text-white">{{ e($guild->Name) }}</h1>
                        <div class="mt-3 flex flex-wrap gap-4 text-xs font-mono">
                            <span class="text-zinc-500">{{ __('ranking.guild_level') }}: <span
                                    class="text-cyan-400 font-bold">{{ $guild->Lvl }}</span></span>
                            <span class="text-zinc-500">{{ __('ranking.members') }}: <span
                                    class="text-violet-400 font-bold">{{ $members->count() }}</span></span>
                            @if ($guild->FoundationDate)
                                <span
                                    class="text-zinc-500">{{ \Carbon\Carbon::parse($guild->FoundationDate)->format('d M Y') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Members Table --}}
            <div class="bg-zinc-900 border border-violet-500/20">
                <div class="px-5 py-4 border-b border-zinc-800">
                    <p class="text-xs font-mono uppercase tracking-[0.25em] text-violet-400/70">
                        {{ __('ranking.guild_members') }}</p>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-800">
                            <th class="text-left px-5 py-3 text-xs font-mono uppercase tracking-wider text-zinc-500">
                                {{ __('ranking.character') }}</th>
                            <th class="text-left px-5 py-3 text-xs font-mono uppercase tracking-wider text-zinc-500">
                                {{ __('ranking.nickname') }}</th>
                            <th class="text-left px-5 py-3 text-xs font-mono uppercase tracking-wider text-zinc-500">
                                {{ __('ranking.join_date') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/60">
                        @foreach ($members as $member)
                            <tr
                                class="hover:bg-violet-500/5 transition {{ (int) $member->MemberClass === 0 ? 'bg-amber-500/5' : '' }}">
                                <td class="px-5 py-3">
                                    <a href="{{ route('ranking.characters.show', $member->CharID) }}"
                                        class="inline-flex items-center gap-1.5 font-medium text-zinc-200 hover:text-violet-300 transition font-mono text-xs uppercase tracking-wide">
                                        @if ((int) $member->MemberClass === 0)
                                            <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endif
                                        {{ e($member->CharName) }}
                                    </a>
                                </td>
                                <td class="px-5 py-3 text-xs font-mono text-zinc-500">{{ e($member->Nickname ?: '—') }}
                                </td>
                                <td class="px-5 py-3 text-xs font-mono text-zinc-500">
                                    @if ($member->JoinDate)
                                        {{ \Carbon\Carbon::parse($member->JoinDate)->format('d M Y') }}
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
