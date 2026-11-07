# Architecture

Landing Funnel Builder is a modular multi-tenant Laravel 12 SaaS.

## Tenant hierarchy

`Platform → Organization → Workspace → Project → (Pages / Funnels / Experiments / Contacts)`

Tenant isolation is enforced with:
- `organization_id` on domain tables
- `BelongsToOrganization` query scopes
- Filament organization panel scoping
- Policies/middleware for module entitlements

## Module registry

Definitions live in `config/modules.php` and sync to `modules` + `organization_modules`.

- Platform can disable modules globally (`platform_enabled`)
- Organizations enable modules included in their plan
- `EnsureModuleEnabled` middleware blocks disabled modules
- Disabling a module does **not** delete tenant data

## Request paths

- Public marketing site: Blade pages under `/`
- Published pages: `/p/{subdomain}` and `/sites/{path}`
- Org admin: Filament `/app`
- Platform admin: Filament `/platform`
- REST API: `/api/v1/*` (Sanctum)
- Editors: Livewire `/editor/pages/{page}` and `/editor/funnels/{funnel}`

## Core services

| Service | Responsibility |
|---|---|
| `ModuleRegistry` | Platform/org module activation |
| `PagePublisher` | Versioning + publish URLs |
| `ExperimentAllocator` | Sticky weighted assignment |
| `StatisticalSummaryService` | Conversion + significance summary |
| `FormCaptureService` | Public form → contact/CRM |
| `WorkflowRunner` | Queued automation actions |
| `AiMarketingAssistant` | AI Hub integration with fake mode |
| `EntitlementService` | Plan limits |
| `AnalyticsTracker` | Event ingestion |
| `FunnelTracker` | Funnel step conversion metrics |
| `PdfReportService` | Unicode PDF reports |

## Admin panel decision

Filament 3 is the primary administration UI (`/app`, `/platform`).

`imrandevbd/laravel-admin-panel` is installed but **not activated** as a competing admin shell because it ships its own RBAC/auth/dashboard stack. See `docs/PACKAGE-INTEGRATION.md`.