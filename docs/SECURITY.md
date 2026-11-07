# Security

- Tenant queries scoped by `organization_id`
- Module middleware blocks disabled features
- Published pages never execute tenant PHP/Blade — JSON sections rendered through allow-listed partials
- Custom HTML sections are stripped to a safe tag subset
- Sanctum protects authenticated API routes
- Integration credentials cast as `encrypted:array`
- No default public super-admin in production docs beyond local demo accounts