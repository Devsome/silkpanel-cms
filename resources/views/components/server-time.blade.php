{{-- Live-ticking current server time (rendered in the server timezone).
     Degrades gracefully to the server-rendered value when Alpine is absent. --}}
@php
    $tz = (isset($timezone) && $timezone !== '') ? $timezone : config('app.timezone', 'UTC');
    $ms = $epochMs ?? (int) round(microtime(true) * 1000);
    $safeInitial = (isset($initial) && $initial !== '')
        ? $initial
        : \Carbon\Carbon::now($tz)->format('H:i:s');
@endphp
<span {{ $attributes }}
    x-data="{ ms: {{ $ms }}, tz: '{{ $tz }}' }"
    x-init="setInterval(() => ms += 1000, 1000)"
    x-text="new Date(ms).toLocaleTimeString('en-GB', { hour12: false, timeZone: tz })"
    title="{{ $tz }}">{{ $safeInitial }}</span>
