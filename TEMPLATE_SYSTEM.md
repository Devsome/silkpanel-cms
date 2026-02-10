# Template System Documentation

## Overview

The Template System allows you to upload and manage custom templates that override default views in SilkPanel CMS. This provides a flexible way to customize the look and feel of your application without modifying core files.

## Features

- **Upload Templates**: Upload custom templates as ZIP files
- **Activate/Deactivate**: Switch between templates or use default views
- **Fallback Mechanism**: Custom views override defaults; missing views fall back to defaults
- **Security**: File validation ensures only safe file types are uploaded
- **Minimalist UI**: Clean, professional interface for template management

## How It Works

### Template Structure

Templates must be uploaded as ZIP files with the following structure:

```
my-template.zip
└── views/
    ├── welcome.blade.php
    ├── layouts/
    │   └── app.blade.php
    ├── components/
    │   └── header.blade.php
    └── ... (other views)
```

### Fallback Mechanism

When a template is active:
1. Laravel checks if the view exists in the active template
2. If found, it uses the custom template view
3. If not found, it falls back to the default view in `resources/views/`

This means you only need to include views you want to customize in your template.

## Usage

### Accessing Template Management

Navigate to `/templates` to access the template management interface.

### Uploading a Template

1. Click "Upload Template"
2. Enter a template name (alphanumeric, hyphens, and underscores only)
3. Select a ZIP file containing your template (max 50MB)
4. Click "Upload Template"

### Activating a Template

1. Go to the Templates page
2. Find your template in the list
3. Click "Activate"
4. The template is now active and will override default views

### Deactivating Templates

1. Click "Deactivate" next to the active template
2. The system will revert to using default views

### Deleting a Template

1. Click "Delete" next to the template you want to remove
2. Confirm the deletion
3. The template and its files will be permanently removed

## File Type Restrictions

For security, only the following file types are allowed in templates:

- `.blade.php` - Blade template files
- `.html` - HTML files
- `.css` - Stylesheets
- `.js` - JavaScript files
- `.json` - JSON files
- `.svg`, `.png`, `.jpg`, `.jpeg`, `.gif`, `.webp` - Image files

Pure PHP files (without `.blade.php` extension) are not allowed.

## Examples

### Example 1: Custom Welcome Page

Create a template with just a custom welcome page:

```
simple-template.zip
└── views/
    └── welcome.blade.php
```

### Example 2: Complete Theme Override

Create a full theme with layout and components:

```
full-theme.zip
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

## Technical Details

### Storage Location

Templates are stored in `storage/app/templates/[template-name]/`

### Database

Template metadata is stored in the `templates` table:
- `id`: Primary key
- `name`: Unique template identifier
- `path`: Path to template directory
- `is_active`: Boolean flag for active template
- `created_at`, `updated_at`: Timestamps

### Custom View Finder

The system uses a custom View Finder (`App\View\CustomViewFinder`) that:
1. Checks for views in active template first
2. Falls back to default Laravel view resolution
3. Maintains compatibility with all Laravel view features

## Security Considerations

- File type validation prevents execution of arbitrary code
- Template names are sanitized
- ZIP extraction is validated
- Only one template can be active at a time
- Templates are stored outside the webroot

## Troubleshooting

### Template Not Activating

- Ensure the ZIP file has the correct structure
- Check that views are in a `views/` directory
- Verify file permissions on `storage/app/templates/`

### Views Not Overriding

- Confirm the template is activated (check for green "Active" badge)
- Verify the view path matches exactly (including subdirectories)
- Check that the file has a `.blade.php` extension

### Upload Fails

- Ensure ZIP file is under 50MB
- Check that only allowed file types are included
- Verify the template name contains only valid characters
