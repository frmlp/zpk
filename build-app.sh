#!/bin/bash
# Make sure this file has executable permissions, run `chmod +x build-app.sh`

# Exit the script if any command fails
set -e

# Build assets using NPM
npm run build

composer install

php artisan key:generate --show

ls -a

touch database/database.sqlite
php artisan migrate --force

# Clear cache
php artisan optimize:clear

# Cache the various components of the Laravel application
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache