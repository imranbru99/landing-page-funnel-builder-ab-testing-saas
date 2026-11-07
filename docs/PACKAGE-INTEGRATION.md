# Package Integration

## imrandevbd/laravel-ai-hub
Primary AI layer. `AiMarketingAssistant` calls `AIHub::prompt()->send()`.
Without provider API keys it uses `AIHub::fake()` / mock content so the app remains usable.

## imrandevbd/laravel-universal-slug
Used via `HasUniversalSlug` on Organization, Workspace, Project, Page, Funnel, Experiment, Form, BlogPost, PageTemplate.
Tenant uniqueness uses `uniqueScope()`.

## imrandevbd/laravel-unicode-pdf
`PdfReportService` renders experiment reports with `UnicodePdf::locale()->loadView()`.

## imrandevbd/drutogoti
Installed for N+1 / slow-query diagnostics in authorized environments. Not exposed to tenants.

## imrandevbd/laravel-ai-catalog
Optional module (`ai_catalog`), platform-disabled by default. Available when product-catalog landing pages are needed.

## imrandevbd/laravel-attendance-hub
Optional internal staff module (`attendance`), platform-disabled by default. Not exposed to marketing customers.

## imrandevbd/laravel-admin-panel — conflict note
This package is a full alternate admin framework (own RBAC, auth, panels, media, audit).
**Decision:** keep Filament as the only admin UI to avoid duplicate permission systems and competing dashboards.
The package remains in Composer for evaluation / selective non-UI utilities, but `admin-panel:install` is intentionally not run.

## spatie/laravel-permission
Available on `User` via `HasRoles` for future granular org roles. Demo uses organization pivot `role` plus platform admin flag.

## laravel/sanctum
REST API personal access tokens.