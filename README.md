# SilkPanel CMS

This project is the newer version of the old [silkroad-laravel](https://github.com/Devsome/silkroad-laravel) project. The technology stack has been updated to Laravel 12, and the project has been restructured to be a full CMS system with a lot of features and modules.

## Documentation

visit the documentation at [https://documentation.devso.me](https://documentation.devso.me) for more details.

## Setup

```bash
git clone git@github.com:Devsome/silkpanel-cms.git silkpanel-cms
cd silkpanel-cms
cp .env.example .env
npm install
npm run build
composer install
php artisan key:generate
```

after that you can open the webpage on the `/install` route.

## License

This project is licensed under the PolyForm Shield License 1.0.0.

You may view and use the source code for personal and educational purposes.
Redistribution, commercial use, or offering the software as a service is prohibited.

Commercial modules are available via API key licensing.

### Features requiring a valid license

The following features are visible in the admin panel without a license, but their
settings are shown as a locked, read-only preview and the feature stays inactive
until a valid SilkPanel license (API key) is configured.

| Feature | Description | Location |
| --- | --- | --- |
| Fake Players | Adds a stable, randomised offset on top of the real online player count shown publicly. The real count stored on the server is never changed. | `Settings → Fake Players` |
| World Map | Live world map on the frontend showing current character positions. | `Settings → World Map` |
| Referral System | Lets players invite others and earn Silk rewards once the referred player reaches the required character level. | `Settings → Referral` |
| Custom Procedures | Maps CMS actions to custom MSSQL stored procedures, including a parameter mapper and test runner. | `Configuration → Custom Procedures` |
| Discord Notifications | Sends configurable Discord webhook notifications for events such as registrations, donations and Webmall sales. | `Integrations → Discord Notifications` |

Breaking the License Terms may result in legal action. Please review the license terms carefully before using or distributing this software.

## For local deployment

```bash
git pull git@github.com:Devsome/silkpanel-cms.git
cd silkpanel-cms
ddev start
ddev composer install
ddev artisan migrate --seed
# ide-helper
ddev artisan ide-helper:generate
ddev artisan ide-helper:models --write
ddev artisan ide-helper:models -N
```
