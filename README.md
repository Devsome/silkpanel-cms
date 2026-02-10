# SilkPanel CMS

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