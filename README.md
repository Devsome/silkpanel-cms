# SilkPanel CMS

A Laravel-based CMS with Filament v5 admin panel, role-based access control using Spatie Permissions, and standard authentication via Laravel Breeze.

## License

This project is licensed under the PolyForm Shield License 1.0.0.

You may view and use the source code for personal and educational purposes.
Redistribution, commercial use, or offering the software as a service is prohibited.

Commercial modules are available via API key licensing.

## local deployment

```bash
git pull git@github.com:Devsome/silkpanel-cms.git
cd silkpanel-cms
ddev start
ddev composer install
ddev artisan migrade --seed
# ide-helper
ddev artisan ide-helper:generate
ddev artisan ide-helper:models --write
ddev artisan ide-helper:models -N
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
php artisan silkpanel:import-accounts {--dry-run : Preview accounts without importing}
```
