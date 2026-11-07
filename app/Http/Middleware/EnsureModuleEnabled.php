<?php

namespace App\Http\Middleware;

use App\Services\Modules\ModuleRegistry;
use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleEnabled
{
    public function __construct(protected ModuleRegistry $modules) {}

    public function handle(Request $request, Closure $next, string $moduleKey): Response
    {
        $organization = TenantContext::organization()
            ?? $request->user()?->currentOrganization
            ?? $request->user()?->organizations()->first();

        if (! $organization) {
            abort(403, 'No organization context.');
        }

        if (! $this->modules->isEnabled($organization, $moduleKey)) {
            abort(403, "Module [{$moduleKey}] is not enabled for this organization.");
        }

        return $next($request);
    }
}
