# Deployment

1. PHP 8.2+, Composer, Node (optional for Vite assets)
2. Configure `.env` (SQLite or MySQL, queue, mail, AI keys)
3. `php artisan migrate --force`
4. `php artisan demo:seed` only on demo environments
5. Run queue worker: `php artisan queue:work`
6. Scheduler: `* * * * * php artisan schedule:run`
7. Serve behind HTTPS; map custom domains to published page hosts