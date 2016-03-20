#!/bin/sh

php artisan route:clear
php artisan cache:clear
php artisan api:cache
