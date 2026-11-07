# Modules

| Key | Name | Default |
|---|---|---|
| landing_pages | Landing Page Builder | core / on |
| websites | Website Builder | off until enabled |
| funnels | Sales Funnel Builder | off until enabled |
| ab_testing | A/B Testing | off until enabled |
| analytics | Analytics | core / on |
| crm | Lead CRM | off until enabled |
| forms | Forms & Popups | off until enabled |
| automation | Marketing Automation | off until enabled |
| templates | Template Marketplace | off until enabled |
| ai_assistant | AI Marketing Assistant | off until enabled |
| domains | Custom Domains | off until enabled |
| billing | Billing & Plans | core / on |
| integrations | Integrations | off until enabled |
| attendance | Staff Attendance | platform disabled |
| ai_catalog | AI Product Catalog | platform disabled |

Enable for an organization:

```php
app(\App\Services\Modules\ModuleRegistry::class)
    ->enableForOrganization($organization, 'funnels');
```

HTTP protection:

```php
Route::middleware(['auth', 'module:funnels'])->group(...);
```