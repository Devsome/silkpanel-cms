# SilkPanel CMS

A Laravel-based CMS with Filament v5 admin panel, role-based access control using Spatie Permissions, and standard authentication via Laravel Breeze.

## Features

- 🎨 **Filament v5** - Modern admin panel at `/admin`
- 🔐 **Role-Based Access Control** - Using Spatie Laravel Permission
- 👥 **User Management** - Admin and Customer roles
- 🔑 **Dual Authentication** - Standard Laravel Breeze auth + Filament admin login

## Quick Start

See [SETUP.md](SETUP.md) for detailed setup instructions including:
- Role configuration
- Test accounts
- Authentication routes
- Security features

more informations are comming soon.

## local deployment

```bash
git pull git@github.com:Devsome/silkpanel-cms.git
cd silkpanel-cms
ddev start
ddev composer install
ddev artisan migrade --seed
```

## ready for deployment

```bash
bash prepare-deploy.sh <local | prod>
```

## informations

more informations are comming soon.


## setup

```bash
php artisan silkpanel:setup
```