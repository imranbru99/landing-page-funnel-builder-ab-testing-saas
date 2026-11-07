# Database

SQLite by default (`DB_CONNECTION=sqlite`).

Major table groups:
- Tenancy: organizations, workspaces, projects, organization_user
- Modules: modules, organization_modules
- Builder: pages, page_versions, page_templates, websites, funnels, funnel_steps, funnel_connections
- Experiments: experiments, experiment_variants, experiment_assignments, experiment_events
- Analytics: analytics_events
- CRM: contacts, tags, pipelines, forms, form_submissions
- Automation: workflows, workflow_runs
- Billing: plans, subscriptions, invoices
- Platform: domains, media_assets, integrations, webhooks, blog_posts, ai_generations