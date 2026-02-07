#!/bin/bash

# Script to switch composer.json between local and production variants
# Usage:
#   bash prepare-deploy.sh local
#   bash prepare-deploy.sh prod

MODE="$1"

if [ -z "$MODE" ]; then
    echo "❌ Missing mode. Use: local | prod"
    exit 1
fi

case "$MODE" in
    local)
        if [ ! -f "composer.local.json" ]; then
            echo "❌ composer.local.json not found!"
            exit 1
        fi

        cp composer.local.json composer.json
        echo "✅ composer.json switched to local (with path repositories)"
        ;;
    prod)
        if [ ! -f "composer.prod.json" ]; then
            echo "❌ composer.prod.json not found!"
            exit 1
        fi

        # Backup current composer.json as local if it contains local repositories
        if grep -q '"packages/\*"' composer.json 2>/dev/null; then
            cp composer.json composer.local.json
            echo "✅ Backup created: composer.local.json"
        fi

        cp composer.prod.json composer.json
        echo "✅ composer.json switched to production (without local packages)"
        ;;
    *)
        echo "❌ Invalid mode: $MODE"
        echo "Use: local | prod"
        exit 1
        ;;
esac

echo ""
echo "📝 Differences:"
diff composer.local.json composer.json || true
