# Template System Setup Guide

## Prerequisites

- Laravel 12 application
- PHP 8.3 or higher
- ZIP extension enabled in PHP

## Installation

The template system is already integrated into SilkPanel CMS. After pulling the latest code:

1. **Run migrations**:
   ```bash
   php artisan migrate
   ```

2. **Ensure storage directory permissions**:
   ```bash
   chmod -R 755 storage/app/templates
   ```

3. **Access the template manager**:
   - Navigate to `http://your-domain.com/templates`

## First Template Upload

### Step 1: Create Your Template

Create a directory structure like this:

```
my-first-template/
└── views/
    └── welcome.blade.php
```

### Step 2: Create welcome.blade.php

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Custom Template</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-16">
        <h1 class="text-4xl font-bold text-center">
            Welcome to My Custom Template!
        </h1>
        <p class="text-center mt-4 text-gray-600">
            This view is loaded from your custom template.
        </p>
    </div>
</body>
</html>
```

### Step 3: Create ZIP File

```bash
zip -r my-first-template.zip my-first-template/
```

### Step 4: Upload via UI

1. Go to `/templates`
2. Click "Upload Template"
3. Enter name: `my-first-template`
4. Select your ZIP file
5. Click "Upload Template"
6. Click "Activate" on your uploaded template

### Step 5: Verify

Visit your homepage (`/`) - you should see your custom welcome page!

## Advanced Usage

### Override Multiple Views

Create a more complex template structure:

```
advanced-template/
└── views/
    ├── welcome.blade.php
    ├── layouts/
    │   └── app.blade.php
    ├── components/
    │   ├── header.blade.php
    │   └── footer.blade.php
    └── partials/
        └── navigation.blade.php
```

### Using Template Assets

You can include assets (CSS, JS, images) in your template:

```
template-with-assets/
└── views/
    ├── welcome.blade.php
    └── assets/
        ├── css/
        │   └── custom.css
        ├── js/
        │   └── custom.js
        └── images/
            └── logo.png
```

Reference them in your views:
```blade
<link rel="stylesheet" href="{{ asset('storage/templates/template-with-assets/views/assets/css/custom.css') }}">
```

## Troubleshooting

### Template Not Appearing

1. Check that the template is activated (green "Active" badge)
2. Verify the view path matches exactly (case-sensitive)
3. Clear Laravel's view cache: `php artisan view:clear`

### Upload Fails

1. Check PHP upload_max_filesize (must be > 50MB)
2. Verify ZIP file contains `views/` directory
3. Ensure only allowed file types are in the ZIP

### Permission Issues

```bash
chmod -R 755 storage/app/templates
chown -R www-data:www-data storage/app/templates
```

## Best Practices

1. **Version Control**: Keep your template source files in version control
2. **Testing**: Test templates locally before uploading to production
3. **Naming**: Use descriptive, lowercase names with hyphens
4. **Backups**: Download templates before deleting them
5. **Documentation**: Document custom features in your templates

## Example Templates

An example template is included in `docs/examples/example-template.zip`.

To use it:
1. Go to `/templates`
2. Upload `docs/examples/example-template.zip`
3. Name it `example-template`
4. Activate it
5. Visit the homepage to see it in action

## Security Notes

- Only safe file types are allowed (.blade.php, .html, .css, .js, .json, images)
- Pure PHP files (without .blade.php) are blocked
- Files are validated during upload
- Templates are stored outside the public webroot
- Server-side validation ensures safe template names

## API Integration (Future)

The template system is designed to support API access in the future:
- List templates via API
- Upload templates programmatically
- Activate/deactivate via API calls

This feature will be added in a future update.
