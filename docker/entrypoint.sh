#!/bin/sh
set -e

echo "Waiting for MySQL to be available..."

until nc -z mysql 3306; do
  echo "MySQL is unavailable - sleeping"
  sleep 2
done

echo "MySQL is up - running migrations"

php artisan migrate --seed --force

echo "Starting Apache..."
apache2-foreground
