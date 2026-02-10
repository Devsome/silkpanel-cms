# SilkPanel CMS

A modern content management system built with Laravel 12.

## Features

### Template System
- **Custom Templates**: Upload and manage custom templates as ZIP files
- **View Override**: Custom views automatically override default views with fallback support
- **Easy Management**: Activate, deactivate, and delete templates through a minimalist UI
- **Secure**: File validation and sanitization for safe template uploads

[Read more about the Template System →](TEMPLATE_SYSTEM.md)

## local deployment

```bash
git pull git@github.com:Devsome/silkpanel-cms.git
cd silkpanel-cms
ddev start
ddev composer install
ddev artisan migrate --seed
```

## ready for deployment

```bash
bash prepare-deploy.sh <local | prod>
```

## setup

```bash
php artisan silkpanel:setup
```

## Template System Quick Start

1. Navigate to `/templates` in your browser
2. Click "Upload Template" 
3. Create a ZIP file with your custom views in a `views/` directory
4. Upload and activate your template

Example template structure:
```
my-template.zip
└── views/
    └── welcome.blade.php
```

See [TEMPLATE_SYSTEM.md](TEMPLATE_SYSTEM.md) for detailed documentation.
