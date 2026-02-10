# Template System Flow Diagrams

## System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                        SilkPanel CMS                             │
│                                                                  │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐     │
│  │   Browser    │───▶│  Controller  │───▶│   Service    │     │
│  │              │◀───│              │◀───│              │     │
│  └──────────────┘    └──────────────┘    └──────────────┘     │
│         │                    │                    │             │
│         │                    │                    │             │
│         ▼                    ▼                    ▼             │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐     │
│  │ Blade Views  │    │  Template    │    │ View Finder  │     │
│  │              │    │  Model       │    │              │     │
│  └──────────────┘    └──────────────┘    └──────────────┘     │
│                              │                    │             │
│                              ▼                    ▼             │
│                       ┌──────────────┐    ┌──────────────┐     │
│                       │   Database   │    │ File System  │     │
│                       └──────────────┘    └──────────────┘     │
└─────────────────────────────────────────────────────────────────┘
```

## Upload Flow

```
┌─────────┐
│  User   │
└────┬────┘
     │
     │ 1. Navigate to /templates/create
     ▼
┌─────────────────────┐
│  Upload Form View   │
└─────────┬───────────┘
          │
          │ 2. Select ZIP & Submit
          ▼
┌─────────────────────┐
│ TemplateController  │
│   store()           │
└─────────┬───────────┘
          │
          │ 3. Validate input
          ▼
┌─────────────────────┐
│  TemplateService    │
│   handleUpload()    │
└─────────┬───────────┘
          │
          │ 4. Extract ZIP
          ▼
┌─────────────────────┐
│  File System        │
│  storage/app/       │
│  templates/{name}/  │
└─────────┬───────────┘
          │
          │ 5. Validate files
          ▼
┌─────────────────────┐
│  Template Model     │
│  Create record      │
└─────────┬───────────┘
          │
          │ 6. Success response
          ▼
┌─────────────────────┐
│  Redirect to list   │
│  with success msg   │
└─────────────────────┘
```

## View Resolution Flow

```
┌─────────────────────┐
│ Laravel View Request│
│   view('welcome')   │
└─────────┬───────────┘
          │
          ▼
┌─────────────────────────────────────┐
│     CustomViewFinder                │
│     findInPaths()                   │
└─────────┬───────────────────────────┘
          │
          │ Is template active?
          ├──── NO ──────────────┐
          │                      │
          │ YES                  │
          ▼                      │
┌─────────────────────┐          │
│ Check custom path:  │          │
│ templates/{active}/ │          │
│ views/welcome.      │          │
│ blade.php           │          │
└─────────┬───────────┘          │
          │                      │
    File exists?                 │
          │                      │
    ┌─── YES                     │
    │     │                      │
    │     │ NO                   │
    │     ▼                      │
    │ ┌─────────────────┐       │
    │ │ Use default:    │◀──────┘
    │ │ resources/views/│
    │ │ welcome.        │
    │ │ blade.php       │
    │ └────────┬────────┘
    │          │
    ▼          │
┌───────────────┴────────┐
│  Return view path      │
└───────────┬────────────┘
            │
            ▼
┌─────────────────────┐
│ Laravel renders view│
└─────────────────────┘
```

## Template Activation Flow

```
┌─────────┐
│  User   │
└────┬────┘
     │
     │ 1. Click "Activate"
     ▼
┌─────────────────────┐
│ TemplateController  │
│   activate()        │
└─────────┬───────────┘
          │
          │ 2. Get template
          ▼
┌─────────────────────┐
│  Template Model     │
│   activate()        │
└─────────┬───────────┘
          │
          │ 3. Deactivate all
          ▼
┌─────────────────────┐
│  UPDATE templates   │
│  SET is_active=0    │
└─────────┬───────────┘
          │
          │ 4. Activate this one
          ▼
┌─────────────────────┐
│  UPDATE templates   │
│  SET is_active=1    │
│  WHERE id = ?       │
└─────────┬───────────┘
          │
          │ 5. Success
          ▼
┌─────────────────────┐
│ Redirect with       │
│ success message     │
└─────────────────────┘
```

## File Structure

```
silkpanel-cms/
│
├── app/
│   ├── Http/Controllers/
│   │   └── TemplateController.php ──── Handles HTTP requests
│   │
│   ├── Models/
│   │   └── Template.php ──────────── Database model
│   │
│   ├── Services/
│   │   └── TemplateService.php ────── Business logic
│   │
│   ├── View/
│   │   └── CustomViewFinder.php ───── View resolution
│   │
│   └── Providers/
│       └── AppServiceProvider.php ─── Service registration
│
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php ──────────── Main layout
│   │
│   └── templates/
│       ├── index.blade.php ────────── Template list
│       └── create.blade.php ───────── Upload form
│
├── database/migrations/
│   └── *_create_templates_table.php ─ Database schema
│
├── storage/app/templates/ ──────────── Template storage
│   ├── .gitkeep
│   └── {template-name}/
│       └── views/
│           └── *.blade.php
│
├── tests/Feature/
│   └── TemplateSystemTest.php ──────── Test suite
│
└── docs/
    ├── TEMPLATE_SYSTEM.md ──────────── Feature docs
    ├── TEMPLATE_SETUP_GUIDE.md ─────── Setup guide
    └── examples/
        └── example-template.zip ────── Example
```

## Security Layers

```
┌─────────────────────────────────────────────────┐
│           Security Validation Stack             │
├─────────────────────────────────────────────────┤
│  Layer 1: Input Validation                      │
│  ├─ Template name regex                         │
│  ├─ File size check (50MB max)                  │
│  └─ MIME type validation                        │
├─────────────────────────────────────────────────┤
│  Layer 2: File Content Validation               │
│  ├─ ZIP integrity check                         │
│  ├─ File extension whitelist                    │
│  ├─ .blade.php enforcement for PHP              │
│  └─ Path traversal prevention                   │
├─────────────────────────────────────────────────┤
│  Layer 3: Storage Security                      │
│  ├─ Storage outside webroot                     │
│  ├─ Proper file permissions                     │
│  └─ No direct web access                        │
├─────────────────────────────────────────────────┤
│  Layer 4: Runtime Security                      │
│  ├─ Laravel CSRF protection                     │
│  ├─ SQL injection protection (Eloquent)         │
│  └─ XSS protection (Blade escaping)             │
└─────────────────────────────────────────────────┘
```

## Data Flow

```
User Action ──▶ Controller ──▶ Service ──▶ Model ──▶ Database
                   │              │           │
                   │              │           └─────▶ Filesystem
                   │              │
                   │              └─────────────────▶ Validation
                   │
                   └────────────────────────────────▶ View
```

## Template Lifecycle

```
┌─────────────┐
│   Created   │  ← ZIP uploaded & extracted
└──────┬──────┘
       │
       │ activate()
       ▼
┌─────────────┐
│   Active    │  ← Views override defaults
└──────┬──────┘
       │
       │ deactivate() or activate(other)
       ▼
┌─────────────┐
│  Inactive   │  ← Views ignored
└──────┬──────┘
       │
       │ delete()
       ▼
┌─────────────┐
│  Deleted    │  ← Files removed, record deleted
└─────────────┘
```
