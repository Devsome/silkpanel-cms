# SilkPanel CMS – Template System Knowledge Base

This file documents all necessary conventions, pitfalls, and patterns for creating and extending templates in the SilkPanel CMS. It serves as a reference for AI agents and developers when building new templates.

---

## Local Development Environment

The project runs locally via **[ddev](https://ddev.readthedocs.io/)**. All CLI commands must be prefixed with `ddev`:

| Task | Command |
|---|---|
| Run Artisan commands | `ddev artisan <command>` |
| Run Yarn commands | `ddev yarn <command>` |

Examples:
```bash
ddev artisan route:list
ddev artisan migrate
ddev yarn dev
ddev yarn build
```

> Never run `php artisan` or `yarn` directly — always use the `ddev` prefix.

---

## Architecture Overview

```
resources/views/
├── templates/
│   ├── aether-gate/          ← dark-cosmic MMORPG template (Tailwind CSS v4, CSS custom properties)
│   ├── neon-strike/          ← dark neon template (Tailwind CSS v4)
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

## Silkroad Online Models (package)

All Silkroad Online database models live in the **`packages/silkroad-models`** package, not in `app/Models/`. Before creating a new model for a Silkroad table, always check whether it already exists there.

```
packages/silkroad-models/src/Models/
├── Account/          ← sro_account connection (e.g. ItemNameDesc, ISRO/VSRO variants)
├── Shard/            ← sro_shard connection (e.g. RefObjCommon, RefObjItem, …)
├── Log/              ← sro_log connection
├── Custom/           ← sro_custom connection
└── Portal/           ← sro_portal connection
```

**Rules:**
- New Silkroad models must be created inside the appropriate subdirectory of `packages/silkroad-models/src/Models/`.
- Use `App\Enums\DatabaseNameEnums` for the `$connection` property (e.g. `DatabaseNameEnums::SRO_SHARD->value`).
- All tables use the `dbo.` schema prefix (e.g. `protected $table = 'dbo._RefObjCommon'`).
- `public $timestamps = false;` on all Silkroad models.
- For cross-database joins (e.g. shard → account for name translations), use the fully-qualified MSSQL syntax: `SILKROAD_R_ACCOUNT.dbo._Rigid_ItemNameDesc`.
- Item/monster name translations: `SILKROAD_R_ACCOUNT.dbo._Rigid_ItemNameDesc`, join on `NameStrID128 = StrID`, English name is in the `ENG` column. Filter out placeholder values (`ENG = '0'`).

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

> **See the full guide at the bottom of this file: [Building a Complete New Template – Full Guide](#building-a-complete-new-template--full-guide)**
> The checklist below covers general CMS conventions; the full guide adds template-specific pitfalls discovered while building aether-gate.

- [ ] All form field names match the controller validation exactly
- [ ] `enctype="multipart/form-data"` present on file upload forms
- [ ] Enum values always compared via `->value`
- [ ] `->getLabel()` used instead of `__('prefix.' . $enum)` for enum display text
- [ ] Route names verified before use (`ddev artisan route:list`)
- [ ] Translations added for all 8 languages
- [ ] No apostrophes inside single-quoted PHP strings
- [ ] Map template uses Leaflet + API — no static `$mapUrl` / `$mapEmbedCode` variables
- [ ] Voting: `$sites` is a Collection of `['site' => VotingSite, 'can_vote' => bool, 'next_vote' => Carbon|null]`
- [ ] Ticket fields: `title` / `text` (not `subject` / `message`), `category_id` (not `category`)
- [ ] No code left after `@endsection` — it will be silently ignored
- [ ] `auth/register.blade.php` includes `silkroad_id` field + global `@if ($errors->any())` block
- [ ] `app_name` passed to auth translation keys: `['app_name' => config('app.name')]`
- [ ] All 4 Livewire ranking views overridden in `livewire/rankings/` (base views use white Tailwind classes)
- [ ] Both event timer files created: `event-timers.blade.php` (sidebar) + `event-timers-list.blade.php`
- [ ] Tooltips in equipment/avatar: use `x-teleport="body"` — never `overflow-hidden` on parent card
- [ ] No `<livewire:rankings.homepage-preview />` — use direct `DB::connection('shard')` query instead


---

## Architektur-Überblick

```
resources/views/
├── templates/
│   ├── aether-gate/          ← dark-cosmic MMORPG Template (Tailwind CSS v4, CSS Custom Properties)
│   ├── neon-strike/          ← dark neon Template (Tailwind CSS v4)
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

> **Die vollständige Anleitung steht am Ende der Datei (englisch): [Building a Complete New Template – Full Guide](#building-a-complete-new-template--full-guide)**

- [ ] Alle Formularfelder exakt nach Controller-Validierung benennen
- [ ] `enctype="multipart/form-data"` bei Datei-Uploads
- [ ] Enum-Werte immer mit `->value` vergleichen
- [ ] `->getLabel()` statt `__('prefix.' . $enum)` für Enum-Texte
- [ ] Route-Namen vor Verwendung prüfen (`ddev artisan route:list`)
- [ ] Translations in alle 8 Sprachen hinzufügen
- [ ] Apostrophe in PHP-Strings: Keine einfachen Hochkommas in Single-Quoted Strings
- [ ] Map-Template: kein `$mapUrl`/`$mapEmbedCode` — das CMS hat Leaflet + API, keine statische Bild-URL
- [ ] Voting: `$sites` ist eine Collection von `['site' => VotingSite, 'can_vote' => bool, 'next_vote' => Carbon|null]`
- [ ] Ticket-Felder: `title`/`text` (nicht `subject`/`message`), `category_id` (nicht `category`)
- [ ] Kein `@endsection` vergessen — nachfolgender Code außerhalb von `@section` wird ignoriert
- [ ] `auth/register.blade.php`: `silkroad_id`-Feld + globaler `@if ($errors->any())`-Block vorhanden
- [ ] `app_name` an Auth-Translation-Keys übergeben: `['app_name' => config('app.name')]`
- [ ] Alle 4 Livewire-Ranking-Views überschrieben in `livewire/rankings/` (Base-Views nutzen weiße Tailwind-Klassen)
- [ ] Beide Event-Timer-Dateien angelegt: `event-timers.blade.php` (Sidebar) + `event-timers-list.blade.php`
- [ ] Tooltips in Equipment/Avatar: `x-teleport="body"` verwenden — kein `overflow-hidden` auf übergeordneten Cards
- [ ] Kein `<livewire:rankings.homepage-preview />` — stattdessen direkter `DB::connection('shard')`-Query

---

## Building a Complete New Template – Full Guide

This section documents everything learned from building the **aether-gate** template. Follow this to create any new template from scratch.

### 1. Directory Structure

```
resources/views/templates/{slug}/
├── template.json                    ← required metadata
├── layouts/
│   └── app.blade.php                ← master layout with CSS design system
├── partials/
│   ├── navigation.blade.php
│   └── footer.blade.php
├── lang/
│   └── en/                          ← template-specific translations (optional)
│       ├── index.php
│       ├── navigation.php
│       ├── footer.php
│       ├── ranking.php
│       └── dashboard.php
├── livewire/                        ← Livewire component view overrides
│   ├── event-timers.blade.php       ← sidebar widget (NOT event-timers-list)
│   ├── event-timers-list.blade.php  ← full list page
│   └── rankings/
│       ├── character-ranking.blade.php
│       ├── guild-ranking.blade.php
│       ├── unique-ranking.blade.php
│       └── custom-ranking.blade.php
├── welcome.blade.php
├── dashboard.blade.php
├── news/
│   ├── index.blade.php
│   └── show.blade.php
├── ranking/
│   ├── characters.blade.php
│   ├── guilds.blade.php
│   ├── character-detail.blade.php
│   └── partials/
│       ├── equipment.blade.php
│       └── avatar.blade.php
├── auth/
│   ├── login.blade.php
│   ├── register.blade.php
│   ├── forgot-password.blade.php
│   └── reset-password.blade.php
├── errors/
│   ├── 404.blade.php
│   └── ... (401, 403, 419, 429, 500, 503)
└── ... (webmall, donation, tickets, voting, downloads, terms)
```

### 2. template.json

```json
{
    "name": "My Template Name",
    "slug": "my-template-slug",
    "version": "1.0.0"
}
```

### 3. Template-Specific Language Files

`TemplateServiceProvider` **automatically** loads `resources/views/templates/{slug}/lang/` if it exists. No registration needed. Use this for keys that differ from global lang files or are template-exclusive.

```php
// resources/views/templates/my-template/lang/en/index.php
return [
    'players_online'  => 'Players Online',
    'live'            => 'Live',
    'featured'        => 'Featured',
    'read_more'       => 'Read More',
    // ...
];
```

Template lang keys override global keys of the same name within the template. Global keys still work — only add keys that are missing or need different wording.

**Keys frequently missing from global lang that templates need:**
- `index.players_online`, `index.live`, `index.featured`, `index.read_more`, `index.max_capacity`
- `index.server_rates`, `index.download_client`, `index.download_now`, `index.get_started`
- `navigation.donation`
- `footer.all_rights_reserved`, `footer.account`, `footer.navigation`
- `ranking.slots_equipped`, `ranking.rank`
- `dashboard.level`, `dashboard.recent_purchases`

### 4. Livewire Component View Resolution

Livewire components resolve their views via `template::livewire.*`. The base views in `resources/views/livewire/` use hardcoded Tailwind light/dark classes (`bg-white dark:bg-gray-900` etc.) — they will look broken in dark custom templates.

**Always create overrides for all ranking components:**

```
livewire/rankings/character-ranking.blade.php
livewire/rankings/guild-ranking.blade.php
livewire/rankings/unique-ranking.blade.php
livewire/rankings/custom-ranking.blade.php
```

**Event timers — two separate components:**
- `EventTimers` → renders `template::livewire.event-timers` (sidebar widget, compact)
- `EventTimersList` → renders `template::livewire.event-timers-list` (full list page)

Both need separate template override files.

### 5. News Model Field Names

```php
// CORRECT                          // WRONG
$news->name                         $news->title
$news->content                      $news->body / $news->excerpt
$news->published_at                 // raw string — NOT cast to Carbon!
$news->slug
$news->thumbnail                    // path only, use asset('storage/' . $news->thumbnail)

// Always wrap published_at in Carbon::parse():
\Carbon\Carbon::parse($news->published_at)->diffForHumans()
\Carbon\Carbon::parse($news->published_at)->format('d M Y')

// For excerpts:
\Illuminate\Support\Str::limit(strip_tags($news->content), 160)
```

### 6. Guild Ranking – Crest Images

The `_Guild` table has a `CrestIcon` binary column. Do NOT render it directly — it will output cryptic strings. The Livewire component exposes a computed `CrestDataUri` property (base64 data URI):

```blade
{{-- In guild-ranking.blade.php override: --}}
@if ($col['column'] === 'CrestIcon' && !empty($row->CrestDataUri))
    <img src="{{ $row->CrestDataUri }}" alt="Crest" class="w-8 h-8">
@else
    {{ e((string) $value) }}
@endif
```

### 7. Tooltip / Popover Overflow Fix

Item tooltips in equipment/avatar partials use absolute positioning and get clipped by `overflow-hidden` on parent cards. Fix: use Alpine.js `x-teleport="body"` with `position: fixed` coordinates from `getBoundingClientRect()`.

```blade
<div x-data="{ show: false, tx: 0, ty: 0 }"
     @mouseenter="show = true; let r = $el.getBoundingClientRect(); tx = r.right + 10; ty = r.top"
     @mouseleave="show = false">

    <button>...</button>

    <template x-teleport="body">
        <div x-show="show" x-cloak
             :style="`position:fixed;left:${tx}px;top:${ty}px;z-index:9999;pointer-events:none;`">
            <x-characters.inventory-tooltip :item="$info" :inline="true" />
        </div>
    </template>
</div>
```

For right-side slots (tooltip should appear to the left):
```blade
:style="`position:fixed;right:${window.innerWidth - tx}px;top:${ty}px;z-index:9999;pointer-events:none;`"
```
where `tx = r.left - 10`.

**Never use `overflow-hidden` on cards that contain absolute-positioned tooltips.**

### 8. Homepage Rankings (welcome.blade.php)

There is **no** `<livewire:rankings.homepage-preview />` component. Query the shard DB directly and cache the result:

```php
$topChars = \Illuminate\Support\Facades\Cache::remember('homepage.ranking.chars', 300, function () {
    try {
        return \Illuminate\Support\Facades\DB::connection('shard')
            ->table('_Char as chars')
            ->leftJoin('_Guild as g', 'chars.GuildID', '=', 'g.ID')
            ->where('chars.DeletedDate', '=', '0001-01-01 00:00:00.000')
            ->where('chars.CharType', 0)
            ->orderByDesc('chars.CurLevel')
            ->orderByDesc('chars.Exp')
            ->limit(5)
            ->select(['chars.CharName16', 'chars.CurLevel', 'g.Name as GuildName'])
            ->get();
    } catch (\Exception $e) {
        return collect();
    }
});
```

### 9. Register Form – Required Fields

The `RegisteredUserController` validates these fields — all must be present in the form:

```
name          → display name
silkroad_id   → game account username (validated by usernameRules())
email
password
password_confirmation
terms         → only if tos_enabled setting is true
referral      → optional
```

Always add a global error block at the top of auth forms:

```blade
@if ($errors->any())
    <div class="...error styles...">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
```

Always pass `app_name` to auth translation keys:
```blade
{{ __('auth/login.title', ['app_name' => config('app.name')]) }}
{{ __('auth/register.title', ['app_name' => config('app.name')]) }}
```

### 10. CSS Design System Pattern (layouts/app.blade.php)

Define all design tokens as CSS custom properties in `:root`, then build utility classes on top. This makes theming consistent and avoids Tailwind class conflicts.

```css
:root {
    --{prefix}-primary: #22d3ee;
    --{prefix}-secondary: #fbbf24;
    --{prefix}-background: #06080f;
    --{prefix}-surface-container: #0d1224;
    --{prefix}-surface-container-low: #080c1a;
    --{prefix}-outline: rgba(255,255,255,0.07);
    --{prefix}-outline-variant: rgba(255,255,255,0.04);
    --{prefix}-on-surface: #e2e8f0;
    --{prefix}-muted: #64748b;
}

/* Then utility classes: */
.{prefix}-card { background: var(--{prefix}-surface-container); border: 1px solid var(--{prefix}-outline); }
.{prefix}-btn-primary { background: var(--{prefix}-primary); ... }
.{prefix}-text-primary { color: var(--{prefix}-primary); }
.{prefix}-text-muted { color: var(--{prefix}-muted); }
.{prefix}-divider { border-color: var(--{prefix}-outline); }
.{prefix}-font-display { font-family: 'YourDisplayFont', sans-serif; }
.{prefix}-font-mono { font-family: 'YourMonoFont', monospace; }
.{prefix}-stat-number { font-family: 'YourMonoFont'; color: var(--{prefix}-primary); }
.{prefix}-section-eyebrow { font-size: 0.65rem; letter-spacing: 0.2em; text-transform: uppercase; color: var(--{prefix}-primary); }
.{prefix}-section-title { font-size: 1.5rem; font-weight: 700; color: var(--{prefix}-on-surface); }
.{prefix}-badge { display: inline-flex; align-items: center; padding: 2px 8px; font-size: 0.65rem; }
.{prefix}-input { background: var(--{prefix}-surface-container-low); border: 1px solid var(--{prefix}-outline); color: var(--{prefix}-on-surface); }
.{prefix}-table th { ... }
.{prefix}-table td { ... }
.{prefix}-rank-1 { color: #fbbf24; }  /* gold */
.{prefix}-rank-2 { color: #94a3b8; }  /* silver */
.{prefix}-rank-3 { color: #b45309; }  /* bronze */
```

Use `@templateStyles` (provided by the layout system) and `@stack('styles')` / `@stack('scripts')` in the layout.

### 11. Page-Specific CSS Animations

Add per-page CSS inside `@push('styles')` blocks, not in the layout:

```blade
@push('styles')
<style>
    @keyframes my-animation { from { opacity: 0; } to { opacity: 1; } }
    .my-element { animation: my-animation 0.4s ease both; }
</style>
@endpush
```

### 12. Complete Template Checklist

- [ ] `template.json` created with correct slug
- [ ] `layouts/app.blade.php` defines full CSS design system with prefixed CSS variables
- [ ] Google Fonts loaded in layout (choose distinctive fonts — NOT Inter/Roboto/Arial)
- [ ] `partials/navigation.blade.php` and `partials/footer.blade.php` created
- [ ] All 4 Livewire ranking overrides created (`livewire/rankings/*.blade.php`)
- [ ] Both event timer overrides created (`livewire/event-timers.blade.php` + `event-timers-list.blade.php`)
- [ ] Template-specific lang files created for missing global keys
- [ ] `auth/register.blade.php` includes `silkroad_id` field + global error block + `app_name` in translation
- [ ] `auth/login.blade.php` passes `app_name` to translation
- [ ] Equipment/avatar partials use `x-teleport="body"` for tooltips — no `overflow-hidden` on parent cards
- [ ] Guild ranking partial handles `CrestDataUri` (not `CrestIcon` raw binary)
- [ ] `News` model: use `->name`, `->content`, `Carbon::parse($news->published_at)`
- [ ] Homepage rankings: direct `DB::connection('shard')` query with `Cache::remember()` — no livewire component
- [ ] No hardcoded percentage values in progress bars — always compute from real data or omit
- [ ] `@push('styles')` used for page-specific CSS, not inline `<style>` in layout
