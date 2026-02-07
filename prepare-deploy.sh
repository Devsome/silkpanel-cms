#!/bin/bash

# Script to prepare deployment to GitHub
# Replaces composer.json with composer.prod.json (without local packages)

echo "🚀 Preparing for GitHub deployment..."

# Check if composer.prod.json exists
if [ ! -f "composer.prod.json" ]; then
    echo "❌ composer.prod.json not found!"
    exit 1
fi

# Backup current composer.json
cp composer.json composer.local.json
echo "Backup created: composer.local.json"

# Replace composer.json with composer.prod.json
cp composer.prod.json composer.json
echo "composer.json prepared for GitHub (without local packages)"

# Show what changed
echo "Differences:"
diff composer.local.json composer.json || true

echo ""
echo "Changes are ready to commit. After push:"
echo "   git restore composer.json"
