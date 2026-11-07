# Demo Setup

```bash
composer install
cp .env.example .env   # if needed
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan demo:seed
php artisan storage:link
php artisan serve
```

## Credentials

| Account | Email | Password | Panel |
|---|---|---|---|
| Demo org owner | demo@landingfunnel.test | password | `/app` |
| Platform admin | platform@landingfunnel.test | password | `/platform` |

Demo organization: **Acme Growth Studio**

Includes:
- 5 published pages
- 3 funnels with connected steps
- Running homepage experiment
- 100 contacts
- Lead capture form + automation workflow
- Growth plan subscription + modules enabled

Published example: `/p/acme-home`