@php
    $item = $item instanceof \Illuminate\Support\Collection ? $item : collect($item ?? []);
    $inline = (bool) ($inline ?? false);
    $positionClasses = $inline
        ? 'relative left-auto top-auto z-auto'
        : 'absolute left-[calc(100%+10px)] top-0 z-[9999]';

    $whiteInfo = collect($item->get('WhiteInfo', []))->filter();
    $blueInfo = collect($item->get('BlueInfo', []));

    $typeID2 = (int) ($item->get('TypeID2') ?? 0);
    $typeID3 = (int) ($item->get('TypeID3') ?? 0);
    $typeID4 = (int) ($item->get('TypeID4') ?? 0);
    $optLevel = (int) ($item->get('OptLevel') ?? 0);
    $nOptValue = (int) ($item->get('nOptValue') ?? 0);
    $soxType = $item->get('SoxType', 'Normal');
@endphp

<div
    class="{{ $positionClasses }} min-w-90 max-w-130 rounded-lg border border-slate-400/40 bg-[#0b1020] p-3 text-gray-200 shadow-[0_14px_28px_rgba(0,0,0,0.4)]">
    @if ($typeID2 === 4)
        <div class="mb-2 font-bold text-[#50cecd]">
            {{ $item->get('ItemName', 'Unknown') }} {{ $optLevel > 0 ? '(+' . $optLevel . ')' : '' }}
        </div>
    @elseif($soxType !== 'Normal' || $blueInfo->isNotEmpty())
        <div class="mb-2 font-bold {{ $soxType !== 'Normal' ? 'text-[#f2e43d]' : 'text-[#50cecd]' }}">
            {{ $item->get('ItemName', 'Unknown') }}
            {{ $optLevel + $nOptValue > 0 ? '(+' . ($optLevel + $nOptValue) . ')' : '' }}
        </div>
    @else
        <div class="mb-2 font-bold">
            {{ $item->get('ItemName', 'Unknown') }}
        </div>
    @endif

    @if ($soxType !== 'Normal' && $typeID2 !== 4)
        <div class="font-bold text-[#f2e43d]">{{ $soxType }}</div>
    @endif

    @if ($item->get('SoxName') && $typeID2 !== 4)
        <div class="font-bold text-[#53ee92]">{{ $item->get('SoxName') }}</div>
    @endif

    <div class="mt-2 text-xs leading-[1.45] text-[#efdaa4]">
        @if ($item->has('Type') && $item->get('Type'))
            {{ __('Sort of item:') }} {{ $item->get('Type') }}<br>
        @endif

        @if ($typeID2 === 1 && !in_array((int) $typeID3, [13, 14], true))

            @if ($item->has('Detail') && $item->get('Detail'))
                {{ __('Mounting part:') }} {{ $item->get('Detail') }}<br>
            @endif

            @if ($typeID2 === 4)
                @if ($item->get('Degree'))
                    {{ __('Level:') }} {{ $item->get('JobDegree') }}<br />
                @endif
            @elseif($typeID2 !== 3)
                @if ($item->get('Degree'))
                    {{ __('Degree: :degree degrees', ['degree' => $item->get('Degree')]) }}<br />
                @endif
            @endif

        @endif
    </div>

    @if ($whiteInfo->isNotEmpty())
        <div class="mt-2 text-xs leading-[1.4]">
            @foreach ($whiteInfo as $white)
                @if (!empty($white))
                    <div>{{ $white }}</div>
                @endif
            @endforeach
        </div>
    @endif

    @if ((int) ($item->get('ReqLevel1') ?? 0) > 0)
        <div class="mt-2 text-xs text-[#efdaa4]">
            {{ __('Reqiure level:') }} {{ $item->get('ReqLevel1') }}
        </div>
    @endif

    @if (in_array((int) $typeID2, [1], true) && !in_array((int) $typeID3, [4, 6], true))
        @if ($item->get('Gender'))
            <div class="text-xs">{{ $item->get('Gender') }}</div>
        @endif
    @endif

    @if (!in_array((int) $typeID2, [3, 4], true) && !in_array((int) $typeID3, [13, 14], true))
        @if ($item->get('Country'))
            <div class="text-xs">{{ $item->get('Country') }}</div>
        @endif
    @endif

    @if (!in_array((int) $typeID2, [4], true) && in_array((int) $typeID3, [13], true) && in_array((int) $typeID4, [2], true))
        <div class="mt-6 text-xs text-[#efdaa4]">{{ __('Attachment:') }}
            {{ $item->get('ChildItemCount') == 1 ? 'Able to equip' : 'Unable to equip' }}</div>
    @else
        <br />
    @endif

    @if (in_array((int) $typeID2, [4], true) || in_array((int) $typeID3, [13], true))
        <div class="text-xs text-[#efdaa4]">
            {{ __('Max. no. of magic options: :unit Unit', ['unit' => $item->get('MaxMagicOptCount')]) }}</div><br />
    @endif

    @if (config('silkpanel.version') === 'vsro' && !in_array((int) $typeID3, [13, 14], true))
        <div class="text-xs text-[#efdaa4]">
            {{ __('Max. no. of magic options: :unit Unit', ['unit' => $item->get('MaxMagicOptCount')]) }}</div><br />
    @endif

    {{-- @if ($typeID3 === 14)
        <div class="text-xs mt-2">
            <span class="text-[#efdaa4]">{{ __('Basic Option') }}</span><br />
            {{ __('MaximumHP :max% Increase', ['max' => $item->get('DevilMaxHP')]) }}<br />
            {{ __('MaximumMP :max% Increase', ['max' => $item->get('DevilMaxHP')]) }}<br />
        </div>
        <br />
    @endif --}}

    @if ($blueInfo->isNotEmpty())
        <div class="mt-2.5 text-xs leading-[1.4]">
            @foreach ($blueInfo as $row)
                @php
                    $code = data_get($row, 'code');
                    $value = (int) data_get($row, 'value', 0);
                    $mValue = (int) data_get($row, 'mValue', 0);
                    $name = data_get($row, 'name');
                    $isDanger = $code === 'MATTR_DEC_MAXDUR' || ($code === 'MATTR_DUR' && $value === 400);
                @endphp
                <div class="font-bold {{ $isDanger ? 'text-[#ff2f51]' : 'text-[#50cecd]' }}">
                    {{ $name }}
                    @if ($mValue > 0)
                        (+{{ max(0, (int) ceil((($value - 1) / ($mValue - 1)) * 100)) }}%)
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @if (!in_array((int) $typeID2, [3, 4], true) && !in_array((int) $typeID3, [13, 14], true))
        <div class="mt-2 text-xs text-[#d2cec4] font-bold">
            @if (!$nOptValue)
                {{ __('Able to use Advanced elixir.') }}
            @else
                {{ __('Advanced elixir is in effect') }} [+{{ $nOptValue }}]
            @endif
        </div>
    @endif

    @if ($typeID3 === 14 && $item->get('TimeEnd'))
        <div class="mt-2 text-xs text-[#efdaa4]">
            <strong>{{ __('Awaken period') }}</strong><br>
            {{ $item->get('TimeEnd') }}
        </div>
    @endif

    @if ((int) ($item->get('Amount') ?? 0) > 0)
        <div class="mt-2 text-xs">{{ __('Quantity') }} {{ $item->get('Amount') }}</div>
    @endif

    @if (auth()->user()->hasAnyRole([App\Enums\UsergroupRoleEnums::ADMIN]))
        <div class="mt-2 text-xs text-[#efdaa4]">{{ __('GM Information:') }}</div>
        <div class="text-xs text-[#ff2f51]">{{ __('ItemID:') }} {{ $item->get('ID64') }}</div>
        <div class="text-xs text-[#ff2f51]">{{ __('RefItemID:') }} {{ $item->get('RefItemID') }}</div>
        <div class="text-xs text-[#ff2f51]">{{ __('Serial64:') }} {{ $item->get('Serial64') }}</div>
    @endif
</div>
