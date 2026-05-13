# SilkPanel CMS – Template System Knowledge Base

This file documents all necessary conventions, pitfalls, and patterns for creating and extending templates in the SilkPanel CMS. It serves as a reference for AI agents and developers when building new templates.

---

## Architecture Overview

```
resources/views/
├── templates/
│   ├── neon-strike/          ← normal template (Tailwind CSS v4, dark neon)
│   ├── gilded-path/          ← reference template (well documented)
│   └── silkroad-gaming/      ← additional reference template
└── dashboard/                ← fallback views (no template namespace)

resources/lang/
└── {locale}/                 ← ar, de, en, es, fr, it, pt, tr
    ├── auth/
    │   ├── login.php
    │   ├── register.php
    │   └── forgot-password.php
    ├── dashboard.php
    ├── donation.php
    ├── downloads.php
    ├── errors.php
    ├── navigation.php
    ├── ranking.php
    ├── terms.php
    ├── tickets.php
    └── voting.php
```

---

## Template Namespace

Templates always use the `template::` namespace:

```blade
@extends('template::layouts.app')
{{-- NOT: @extends('neon-strike::layouts.app') --}}
```

The active template path is resolved via `SettingHelper` or CMS configuration. The `template::` namespace is always correct regardless of the active template.

---

## Layouts & Sections

Every template page uses:

```blade
@extends('template::layouts.app')

@push('styles')
    {{-- Page-specific CSS --}}
@endpush

@section('content')
    {{-- Page content --}}
@endsection

@push('scripts')
    {{-- Page-specific JS --}}
@endpush
```

---

## Translations

### Rule
Translations must **always** be added in all 8 languages: `ar, de, en, es, fr, it, pt, tr`

### Structure
```php
// resources/lang/en/tickets.php
return [
    'title' => 'Support Tickets',
    'form' => [
        'subject' => 'Subject',  // nested with dot-notation: tickets.form.subject
    ],
];
```

### Common Pitfalls
- **Apostrophes in PHP single-quoted strings**: `'Don't'` → Parse error! Use `"Don't"` or `'Don\'t'`
- **Nested keys must be called correctly**: `__('errors.404.message')` not `__('errors.404')`
- **Enum labels**: Never use `__('tickets.status_' . $ticket->status)` — throws an error when `$status` is an Enum. Use `$ticket->status->getLabel()` instead.

### Required Keys per File

**tickets.php** – all keys:
`section_label, title, back, new_ticket, create_ticket, no_tickets, reply, send_reply, close_ticket, reopen_ticket, staff, you, status_open, status_answered, status_closed, form.subject, form.category, form.select_category, form.message, form.submit, form.cancel, form.attachments, form.attachments_hint`

**voting.php** – all keys:
`section_label, title, vote_now, no_sites, silk_reward, cooldown`

**donation.php** – all keys:
`section_label, back, packages, select, pay_with, featured, or_redeem, epin_label, redeem, success_label, success_description, go_to_dashboard, cancel_label, cancel_description, try_again`

**errors.php** – structure:
```php
return [
    '401' => ['title' => '...', 'message' => '...'],
    '403' => ['title' => '...', 'message' => '...'],
    '404' => ['title' => '...', 'message' => '...'],
    '419' => ['title' => '...', 'message' => '...'],
    '429' => ['title' => '...', 'message' => '...'],
    '500' => ['title' => '...', 'message' => '...'],
    '503' => ['title' => '...', 'message' => '...'],
    'go_home'     => '...',
    'go_back'     => '...',
    'retry_later' => '...',
];
```

**dashboard.php** – map keys (for Leaflet widget):
`world_map, map_subtitle, map_search, map_online, map_max_shown, map_updated, map_loading, map_refresh, map_manual, map_level, map, back_to_dashboard`

---

## Routes & Controller Conventions

### Route Overview (critical)

| Route name | Method | Controller/Closure |
|---|---|---|
| `dashboard` | GET | `DashboardController@index` |
| `dashboard.silk-history` | GET | `DashboardController@silkHistory` |
| `dashboard.map` | GET | Closure (checks `map_frontend_enabled`) |
| `voting.index` | GET | `VotingController@index` |
| `webmall.index` | GET | Closure (checks `webmall_enabled`) |
| `tickets.index` | GET | `TicketController@index` |
| `tickets.create` | GET | `TicketController@create` |
| `tickets.store` | POST | `TicketController@store` |
| `tickets.show` | GET | `TicketController@show` |
| `tickets.reply` | POST `{id}` | `TicketController@addReply` |
| `tickets.close` | POST `{id}` | `TicketController@close` |
| `tickets.reopen` | POST `{id}` | `TicketController@reopen` |
| `donate.index` | GET | `DonationController@index` |
| `donate.packages` | GET `{provider}` | `DonationController@packages` |
| `donate.redeem-epin.show` | GET `{provider}` | `DonationController@showRedeemEpin` |
| `donate.redeem-epin` | POST `{provider}` | `DonationController@redeemEpin` |
| `donate.checkout` | POST `{package}` | `DonationController@checkout` |

### Always verify route names
Before using `route()` in a template, verify the exact name:
```bash
php artisan route:list --name=tickets
```

---

## Model Field Names (critical!)

### Ticket
```php
// Fields: id, user_id, category_id, title, priority, status, text
// NOT: subject, message

$ticket->title      // title (NOT subject)
$ticket->text       // body content (NOT message/body)
$ticket->status     // TicketStatus enum (NOT string)
$ticket->priority   // TicketPriority enum (NOT string)
$ticket->category   // BelongsTo TicketCategory
```

### TicketReply
```php
// Fields: id, ticket_id, user_id, text, is_admin_reply
// NOT: message, is_staff_reply

$reply->text            // body content (NOT message)
$reply->is_admin_reply  // boolean (NOT is_staff_reply)
```

### TicketStatus Enum (values)
```php
// Case::value:
'open', 'in_progress', 'reopened', 'closed'
// NOT: 'answered'

// Usage in templates:
$ticket->status->value      // for string comparison
$ticket->status->getLabel() // for localised display text
```

### TicketPriority Enum
```php
'low', 'medium', 'high', 'urgent'
$ticket->priority->value
$ticket->priority->getLabel()
```

### Form fields for tickets.store
```
title         (NOT: subject)
category_id   (integer, NOT: category as string)
priority      (string enum-value, e.g. 'medium')
text          (NOT: message)
attachments[] (optional, max 5, images only)
```

### VotingSite (from Voting package)
```php
$site->name
$site->url
$site->image            // optional, image URL
$site->reward           // integer, silk reward amount
$site->reward_silk_type
$site->timeout_hours
$site->is_active
$site->sort_order
```

### VotingController – Data Structure
The controller passes `compact('sites')` — a Collection of arrays:
```php
$sites = collect([
    [
        'site'      => VotingSite $site,
        'can_vote'  => bool,
        'next_vote' => Carbon|null,  // when the user can vote again
    ],
    // ...
]);
```
Template iteration:
```blade
@foreach ($sites as $item)
    @php
        $site     = $item['site'];
        $canVote  = $item['can_vote'];
        $nextVote = $item['next_vote'];
    @endphp
@endforeach
```

### DonationPackage
```php
$package->name
$package->description
$package->silk_amount
$package->price
$package->currency
$package->image         // optional
$package->is_featured
```

---

## Enums – Conventions

All enums expose `->value` (string) and `->getLabel()` (localised string).

**Never** compare directly with a string — always use `$enum->value === 'string'` or `$enum === EnumClass::Case`.

```blade
{{-- WRONG --}}
@if ($ticket->status === 'closed')
match ($ticket->status) { 'open' => '...' }

{{-- CORRECT --}}
@if ($ticket->status->value === 'closed')
match ($ticket->status->value) { 'open' => '...' }
{{ $ticket->status->getLabel() }}
```

---

## Settings / SettingHelper

```php
// PHP
\App\Models\Setting::get('key', 'default')
\App\Helpers\SettingHelper::get('key', 'default')

// Blade @php block
@php
    $enabled  = (bool) \App\Models\Setting::get('map_frontend_enabled', false);
    $interval = max(10, (int) \App\Models\Setting::get('map_refresh_interval', 30));
@endphp
```

Known setting keys:

| Key | Type | Description |
|---|---|---|
| `map_frontend_enabled` | bool | Map visible to users |
| `map_refresh_interval` | int | Seconds between API polling |
| `map_max_characters` | int | Max characters shown on map |
| `map_default_lat/lng/zoom` | float/int | Leaflet initial position |
| `map_tile_url` | string | Custom Leaflet tile server URL |
| `webmall_enabled` | bool | Webmall visible to users |
| `is_ticket_system_enabled` | bool | Ticket system active |

---

## Leaflet Map (dashboard/map)

The map is powered by Leaflet + `public/js/silkpanel-map.js` + an API endpoint. There is **no static map image URL** — do not use `$mapUrl` or `$mapEmbedCode`.

```blade
@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
@endpush

@php
    $refreshInterval = max(10, (int) \App\Models\Setting::get('map_refresh_interval', 30));
    $maxChars = (int) \App\Models\Setting::get('map_max_characters', \App\Services\SilkroadMapService::MAX_CHARACTERS);
@endphp

{{-- Container must have x-data="silkroadMap" --}}
<div x-data="silkroadMap">
    <div id="map"></div>
</div>

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="{{ asset('js/silkpanel-map.js') }}"></script>
    <script>
        window._silkroadMapConfig = {
            apiUrl: @js(route('api.map.characters')),
            refreshInterval: {{ $refreshInterval }},
        };
    </script>
@endpush
```

---

## Ticket System – File Attachments

Forms that upload files require `enctype="multipart/form-data"`.

```blade
<form method="POST" action="..." enctype="multipart/form-data">
    @csrf
    {{-- ... --}}
    <input type="file" name="attachments[]" multiple
           accept="image/jpeg,image/png,image/gif,image/webp">
</form>
```

Displaying attachments:
```blade
@if ($reply->attachments->count())
    @foreach ($reply->attachments as $att)
        <a href="{{ Storage::url($att->file_path) }}" target="_blank">
            <img src="{{ Storage::url($att->file_path) }}" alt="{{ e($att->original_name) }}">
        </a>
    @endforeach
@endif
```

---

## Donations

### Provider listing (donate.index)
```blade
{{-- $providers = Collection of provider objects/arrays --}}
<a href="{{ route('donate.packages', $provider) }}">...</a>
```

### Packages (donate.packages → {provider})
```blade
{{-- $provider, $packages --}}
<form method="POST" action="{{ route('donate.checkout', $package) }}">
    @csrf
    <input type="hidden" name="provider" value="{{ $provider }}">
    <button type="submit">...</button>
</form>
```

### E-Pin redemption
```blade
{{-- Route: donate.redeem-epin (POST) with {provider} parameter --}}
<form method="POST" action="{{ route('donate.redeem-epin', $provider) }}">
    @csrf
    <input type="text" name="epin" required>
    <button type="submit">...</button>
</form>
```

---

## Error Pages

Error pages live in `resources/views/templates/{name}/errors/`.

File names: `401.blade.php, 403.blade.php, 404.blade.php, 419.blade.php, 429.blade.php, 500.blade.php, 503.blade.php`

Correct translation calls (nested structure!):
```blade
{{ __('errors.404.title') }}    {{-- CORRECT --}}
{{ __('errors.404.message') }}  {{-- CORRECT --}}
{{ __('errors.404') }}          {{-- WRONG – returns an array --}}
```

---

## Ticket Package – Reference Views

The ticket package ships its own fallback views at:
`packages/ticket-system/src/Views/`

These are used when `template::tickets.{name}` does not exist. Use these as the authoritative reference for field names and logic when building a new template.

---

## Checklist for New Templates

- [ ] All form field names match the controller validation exactly
- [ ] `enctype="multipart/form-data"` present on file upload forms
- [ ] Enum values always compared via `->value`
- [ ] `->getLabel()` used instead of `__('prefix.' . $enum)` for enum display text
- [ ] Route names verified before use (`php artisan route:list`)
- [ ] Translations added for all 8 languages
- [ ] No apostrophes inside single-quoted PHP strings
- [ ] Map template uses Leaflet + API — no static `$mapUrl` / `$mapEmbedCode` variables
- [ ] Voting: `$sites` is a Collection of `['site' => VotingSite, 'can_vote' => bool, 'next_vote' => Carbon|null]`
- [ ] Ticket fields: `title` / `text` (not `subject` / `message`), `category_id` (not `category`)
- [ ] No code left after `@endsection` — it will be silently ignored


---

## Architektur-Überblick

```
resources/views/
├── templates/
│   ├── neon-strike/          ← aktives Template (Tailwind CSS v4, dark neon)
│   ├── gilded-path/          ← Referenz-Template (gut dokumentiert)
│   └── silkroad-gaming/      ← weiteres Referenz-Template
└── dashboard/                ← Fallback-Views (kein Template-Namespace)

resources/lang/
└── {locale}/                 ← ar, de, en, es, fr, it, pt, tr
    ├── auth/
    │   ├── login.php
    │   ├── register.php
    │   └── forgot-password.php
    ├── dashboard.php
    ├── donation.php
    ├── downloads.php
    ├── errors.php
    ├── navigation.php
    ├── ranking.php
    ├── terms.php
    ├── tickets.php
    └── voting.php
```

---

## Template-Namespace

Templates verwenden immer den Namespace `template::`:

```blade
@extends('template::layouts.app')
{{-- NICHT: @extends('neon-strike::layouts.app') --}}
```

Der aktive Template-Pfad wird über `SettingHelper` oder die CMS-Konfiguration aufgelöst. Der Namespace `template::` ist immer korrekt, unabhängig vom gewählten Template.

---

## Layouts & Sections

Jede Template-Seite verwendet:

```blade
@extends('template::layouts.app')

@push('styles')
    {{-- Seiten-spezifisches CSS --}}
@endpush

@section('content')
    {{-- Seiteninhalt --}}
@endsection

@push('scripts')
    {{-- Seiten-spezifische JS --}}
@endpush
```

---

## Design Tokens – neon-strike

| Element | Klassen |
|---|---|
| Card | `bg-zinc-900 border border-violet-500/20` |
| Card hover | `hover:border-violet-500/35` |
| Primärer Button | `bg-linear-to-r from-violet-600 to-fuchsia-600 text-white shadow-[0_0_20px_rgba(139,92,246,0.4)]` |
| Button hover | `hover:from-violet-500 hover:to-fuchsia-500` |
| Section-Label | `text-xs font-mono uppercase tracking-[0.3em] text-violet-400/70` |
| Gradient-Heading | `bg-gradient-to-r from-violet-400 via-fuchsia-400 to-cyan-400 bg-clip-text text-transparent` |
| Divider | `h-px bg-linear-to-r from-violet-500/40 to-transparent` |
| Input | `bg-zinc-950 border border-zinc-700 text-zinc-100 px-3 py-2.5 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30 font-mono text-sm transition` |
| Deaktiviert/Cooldown | `text-zinc-600 border-zinc-800 cursor-not-allowed` |
| Staff-Farbe | `text-fuchsia-400 border-fuchsia-500/25 bg-fuchsia-500/5` |
| Fehler | `border-red-500/30 bg-red-500/10 text-red-400` |
| Erfolg | `border-violet-500/40 bg-violet-500/10 text-violet-300` |
| Info/Cyan | `text-cyan-400 border-cyan-500/40 bg-cyan-500/10` |

---

## Translations

### Grundregel
Translations **immer** in allen 8 Sprachen hinzufügen: `ar, de, en, es, fr, it, pt, tr`

### Struktur
```php
// resources/lang/en/tickets.php
return [
    'title' => 'Support Tickets',
    'form' => [
        'subject' => 'Subject',  // nested mit Punkt-Notation: tickets.form.subject
    ],
];
```

### Häufige Fallstricke
- **Apostrophe in PHP Single-Quotes**: `'Don't'` → Parse-Fehler! Entweder `"Don't"` oder `'Don\'t'`
- **Nested Keys korrekt aufrufen**: `__('errors.404.message')` nicht `__('errors.404')`
- **Enum-Labels**: Nie `__('tickets.status_' . $ticket->status)` — gibt Fehler wenn `$status` ein Enum ist. Stattdessen `$ticket->status->getLabel()`

### Pflicht-Keys nach Datei

**tickets.php** – alle Keys:
`section_label, title, back, new_ticket, create_ticket, no_tickets, reply, send_reply, close_ticket, reopen_ticket, staff, you, status_open, status_answered, status_closed, form.subject, form.category, form.select_category, form.message, form.submit, form.cancel, form.attachments, form.attachments_hint`

**voting.php** – alle Keys:
`section_label, title, vote_now, no_sites, silk_reward, cooldown`

**donation.php** – alle Keys:
`section_label, back, packages, select, pay_with, featured, or_redeem, epin_label, redeem, success_label, success_description, go_to_dashboard, cancel_label, cancel_description, try_again`

**errors.php** – Struktur:
```php
return [
    '401' => ['title' => '...', 'message' => '...'],
    '403' => ['title' => '...', 'message' => '...'],
    '404' => ['title' => '...', 'message' => '...'],
    '419' => ['title' => '...', 'message' => '...'],
    '429' => ['title' => '...', 'message' => '...'],
    '500' => ['title' => '...', 'message' => '...'],
    '503' => ['title' => '...', 'message' => '...'],
    'go_home'     => '...',
    'go_back'     => '...',
    'retry_later' => '...',
];
```

**dashboard.php** – Map-Keys (für Leaflet-Widget):
`world_map, map_subtitle, map_search, map_online, map_max_shown, map_updated, map_loading, map_refresh, map_manual, map_level, map, back_to_dashboard`

---

## Routen & Controller-Konventionen

### Routen-Übersicht (kritische)

| Route-Name | Methode | Controller/Closure |
|---|---|---|
| `dashboard` | GET | `DashboardController@index` |
| `dashboard.silk-history` | GET | `DashboardController@silkHistory` |
| `dashboard.map` | GET | Closure (prüft `map_frontend_enabled`) |
| `voting.index` | GET | `VotingController@index` |
| `webmall.index` | GET | Closure (prüft `webmall_enabled`) |
| `tickets.index` | GET | `TicketController@index` |
| `tickets.create` | GET | `TicketController@create` |
| `tickets.store` | POST | `TicketController@store` |
| `tickets.show` | GET | `TicketController@show` |
| `tickets.reply` | POST `{id}` | `TicketController@addReply` |
| `tickets.close` | POST `{id}` | `TicketController@close` |
| `tickets.reopen` | POST `{id}` | `TicketController@reopen` |
| `donate.index` | GET | `DonationController@index` |
| `donate.packages` | GET `{provider}` | `DonationController@packages` |
| `donate.redeem-epin.show` | GET `{provider}` | `DonationController@showRedeemEpin` |
| `donate.redeem-epin` | POST `{provider}` | `DonationController@redeemEpin` |
| `donate.checkout` | POST `{package}` | `DonationController@checkout` |

### Wichtig: Route-Namen prüfen
Vor dem Verwenden von `route()` im Template immer den exakten Namen verifizieren:
```bash
php artisan route:list --name=tickets
```

---

## Model-Feldnamen (kritisch!)

### Ticket
```php
// Felder: id, user_id, category_id, title, priority, status, text
// NICHT: subject, message

$ticket->title      // Titel (NICHT subject)
$ticket->text       // Inhalt (NICHT message/body)
$ticket->status     // TicketStatus-Enum (NICHT string)
$ticket->priority   // TicketPriority-Enum (NICHT string)
$ticket->category   // BelongsTo TicketCategory
```

### TicketReply
```php
// Felder: id, ticket_id, user_id, text, is_admin_reply
// NICHT: message, is_staff_reply

$reply->text            // Inhalt (NICHT message)
$reply->is_admin_reply  // Boolean (NICHT is_staff_reply)
```

### TicketStatus-Enum (Werte)
```php
// Case::value:
'open', 'in_progress', 'reopened', 'closed'
// NICHT: 'answered'

// Verwendung im Template:
$ticket->status->value   // string-Vergleich
$ticket->status->getLabel() // lokalisierter Text
```

### TicketPriority-Enum
```php
'low', 'medium', 'high', 'urgent'
$ticket->priority->value
$ticket->priority->getLabel()
```

### Formular-Felder für tickets.store
```
title         (NICHT: subject)
category_id   (Integer, NICHT: category als string)
priority      (string enum-value, z.B. 'medium')
text          (NICHT: message)
attachments[] (optional, max 5, nur Bilder)
```

### VotingSite (aus Voting-Package)
```php
$site->name
$site->url
$site->image     // optional, URL zum Bild
$site->reward    // Integer, Silk-Belohnung
$site->reward_silk_type
$site->timeout_hours
$site->is_active
$site->sort_order
```

### VotingController – Datenstruktur
Der Controller übergibt `compact('sites')` — eine Collection von Arrays:
```php
$sites = collect([
    [
        'site'      => VotingSite $site,
        'can_vote'  => bool,
        'next_vote' => Carbon|null,  // Zeitpunkt wann wieder abstimmbar
    ],
    // ...
]);
```
Template-Iteration:
```blade
@foreach ($sites as $item)
    @php
        $site     = $item['site'];
        $canVote  = $item['can_vote'];
        $nextVote = $item['next_vote'];
    @endphp
@endforeach
```

### DonationPackage
```php
$package->name
$package->description
$package->silk_amount
$package->price
$package->currency
$package->image     // optional
$package->is_featured
```

---

## Enums – Konventionen

Alle Enums haben `->value` (string) und `->getLabel()` (lokalisiert).

**Nie** direkt mit `$enum == 'string'` vergleichen — immer `$enum->value === 'string'` oder `$enum === EnumClass::Case`.

```blade
{{-- FALSCH --}}
@if ($ticket->status === 'closed')
match ($ticket->status) { 'open' => '...' }

{{-- RICHTIG --}}
@if ($ticket->status->value === 'closed')
match ($ticket->status->value) { 'open' => '...' }
{{ $ticket->status->getLabel() }}
```

---

## Settings / SettingHelper

```php
// PHP
\App\Models\Setting::get('key', 'default')
\App\Helpers\SettingHelper::get('key', 'default')

// Blade @php-Block
@php
    $enabled = (bool) \App\Models\Setting::get('map_frontend_enabled', false);
    $interval = max(10, (int) \App\Models\Setting::get('map_refresh_interval', 30));
@endphp
```

Bekannte Setting-Keys:
| Key | Typ | Beschreibung |
|---|---|---|
| `map_frontend_enabled` | bool | Karte für User sichtbar |
| `map_refresh_interval` | int | Sekunden zwischen API-Calls |
| `map_max_characters` | int | Max. angezeigte Chars auf Karte |
| `map_default_lat/lng/zoom` | float/int | Leaflet-Startwerte |
| `map_tile_url` | string | Eigener Leaflet Tile-Server |
| `webmall_enabled` | bool | Webmall für User sichtbar |
| `is_ticket_system_enabled` | bool | Ticket-System aktiv |

---

## Leaflet-Karte (dashboard/map)

Die Karte basiert auf Leaflet + `public/js/silkpanel-map.js` + einem API-Endpoint.

```blade
@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
@endpush

@php
    $refreshInterval = max(10, (int) \App\Models\Setting::get('map_refresh_interval', 30));
    $maxChars = (int) \App\Models\Setting::get('map_max_characters', \App\Services\SilkroadMapService::MAX_CHARACTERS);
@endphp

{{-- Im Template: x-data="silkroadMap" auf dem Container --}}
<div x-data="silkroadMap">
    <div id="map"></div>
</div>

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="{{ asset('js/silkpanel-map.js') }}"></script>
    <script>
        window._silkroadMapConfig = {
            apiUrl: @js(route('api.map.characters')),
            refreshInterval: {{ $refreshInterval }},
        };
    </script>
@endpush
```

---

## Ticket-System – Anhänge (Datei-Upload)

Formulare mit Datei-Upload brauchen `enctype="multipart/form-data"`.

```blade
<form method="POST" action="..." enctype="multipart/form-data">
    @csrf
    {{-- ... --}}
    <input type="file" name="attachments[]" multiple
           accept="image/jpeg,image/png,image/gif,image/webp">
</form>
```

Anhänge anzeigen:
```blade
@if ($reply->attachments->count())
    @foreach ($reply->attachments as $att)
        <a href="{{ Storage::url($att->file_path) }}" target="_blank">
            <img src="{{ Storage::url($att->file_path) }}" alt="{{ e($att->original_name) }}">
        </a>
    @endforeach
@endif
```

---

## Donations

### Provider-Listing (donate.index)
```blade
{{-- $providers = Collection von Provider-Objekten/Arrays --}}
<a href="{{ route('donate.packages', $provider) }}">...</a>
```

### Packages (donate.packages → {provider})
```blade
{{-- $provider, $packages --}}
<form method="POST" action="{{ route('donate.checkout', $package) }}">
    @csrf
    <input type="hidden" name="provider" value="{{ $provider }}">
    <button type="submit">...</button>
</form>
```

### E-Pin Einlösen
```blade
{{-- Route: donate.redeem-epin (POST) mit {provider} Parameter --}}
<form method="POST" action="{{ route('donate.redeem-epin', $provider) }}">
    @csrf
    <input type="text" name="epin" required>
    <button type="submit">...</button>
</form>
```

---

## Error-Pages

Fehlerseiten befinden sich in `resources/views/templates/{name}/errors/`.

Dateinamen: `401.blade.php, 403.blade.php, 404.blade.php, 419.blade.php, 429.blade.php, 500.blade.php, 503.blade.php`

Korrekte Translation-Aufrufe (nested!):
```blade
{{ __('errors.404.title') }}    {{-- RICHTIG --}}
{{ __('errors.404.message') }}  {{-- RICHTIG --}}
{{ __('errors.404') }}          {{-- FALSCH – gibt Array zurück --}}
```

---

## Ticket-Packages – Referenz-Views

Das Ticket-Package enthält eigene Fallback-Views unter:
`packages/ticket-system/src/Views/`

Diese werden verwendet wenn `template::tickets.{name}` nicht existiert. Beim Bauen eines neuen Templates diese Views als Referenz für Feldnamen und Logik nutzen.

---

## Häufige Fehlerquellen (Checkliste beim neuen Template)

- [ ] Alle Formularfelder exakt nach Controller-Validierung benennen
- [ ] `enctype="multipart/form-data"` bei Datei-Uploads
- [ ] Enum-Werte immer mit `->value` vergleichen
- [ ] `->getLabel()` statt `__('prefix.' . $enum)` für Enum-Texte
- [ ] Route-Namen vor Verwendung prüfen (`php artisan route:list`)
- [ ] Translations in alle 8 Sprachen hinzufügen
- [ ] Apostrophe in PHP-Strings: Keine einfachen Hochkommas in Single-Quoted Strings
- [ ] Map-Template: kein `$mapUrl`/`$mapEmbedCode` — das CMS hat Leaflet + API, keine statische Bild-URL
- [ ] Voting: `$sites` ist eine Collection von `['site' => VotingSite, 'can_vote' => bool, 'next_vote' => Carbon|null]`
- [ ] Ticket-Felder: `title`/`text` (nicht `subject`/`message`), `category_id` (nicht `category`)
- [ ] Kein `@endsection` vergessen — nachfolgender Code außerhalb von `@section` wird ignoriert
