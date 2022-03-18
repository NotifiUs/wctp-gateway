# Puts the application into maintenance mode
# Web visits will receive a 503 error
# SMS and WCTP endpoints will still accept messages to be queued
# Messages will not send until deployment is over.
php artisan down

# Clears the cache
php artisan optimize:clear

# You may need to setup git to point to github.com/notifius/wctp-gateway
git pull origin main

# Run composer as normal user (not root)
sudo -u "$USER" composer install --no-interaction --prefer-dist --optimize-autoloader

# Restart nginx and php-fpm services
# Adjust your php version if neccessary
sudo service php8.1-fpm restart
sudo service nginx restart

# Migrate any new database updates
php artisan migrate --force

# Clears out old telescope events/logs
php artisan telescope:clear

# Kills horizon so supervisord can restart it with the latest changes
sudo php artisan horizon:terminate

# Cache our configuration files for better performance
php artisan config:cache

# Bring the application back up.
php artisan up
