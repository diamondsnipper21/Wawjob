composer dump-autoload



========== Reverse "timezones" table into Seed ==========
php artisan iseed email_templates
php artisan iseed notifications


========== Run
php artisan db:seed
php artisan db:seed --class=EmailTemplatesTableSeeder
php artisan db:seed --class=NotificationsTableSeeder