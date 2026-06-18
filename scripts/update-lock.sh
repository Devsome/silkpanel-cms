#!/usr/bin/env bash
# Generates a composer.lock using the live registry (composer.devso.me)
# instead of the local path repositories.
# Run this before committing composer.lock after releasing new package versions.

set -e

COMPOSER_JSON="composer.json"
BACKUP="composer.json.local-backup"

echo "Switching to live repositories..."
cp "$COMPOSER_JSON" "$BACKUP"

python3 -c "
import json

with open('$COMPOSER_JSON') as f:
    data = json.load(f)

data['repositories'] = [
    {
        'type': 'composer',
        'url': 'https://composer.devso.me'
    }
]

with open('$COMPOSER_JSON', 'w') as f:
    json.dump(data, f, indent=4)
print('Done: repositories switched to live registry.')
"

echo "Running composer update..."
ddev composer update --no-interaction

echo "Restoring local composer.json..."
mv "$BACKUP" "$COMPOSER_JSON"

echo ""
echo "Done! composer.lock updated. You can now commit it."
