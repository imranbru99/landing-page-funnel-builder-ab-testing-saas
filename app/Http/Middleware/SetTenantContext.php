<?php

namespace App\Http\Middleware;

use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenantContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            TenantContext::set(
                $user->currentOrganization,
                $user->currentWorkspace,
                $user->currentProject
            );
        }

        try {
            return $next($request);
        } finally {
            TenantContext::clear();
        }
    }
}
