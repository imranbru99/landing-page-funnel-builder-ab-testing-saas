# User Roles

## Platform
- `users.is_platform_admin` gates `/platform`
- Intended roles: super admin, support, billing, analyst (extend via Spatie roles)

## Organization
Stored on `organization_user.role` (owner/admin/member/...) and Filament `/app` access requires org membership.

Authorization must never rely on hidden UI alone — use policies/middleware for mutations.