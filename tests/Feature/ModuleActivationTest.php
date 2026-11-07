<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureModuleEnabled;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\User;
use App\Services\Modules\ModuleRegistry;
use App\Support\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ModuleActivationTest extends TestCase
{
    use RefreshDatabase;

    public function test_disabled_module_is_blocked_by_middleware(): void
    {
        $registry = app(ModuleRegistry::class);
        $registry->syncDefinitions();

        $plan = Plan::query()->create(['name' => 'T', 'slug' => 't', 'price_cents' => 0]);
        $user = User::factory()->create();
        $org = Organization::query()->create(['name' => 'Org', 'owner_id' => $user->id, 'plan_id' => $plan->id]);
        $user->update(['current_organization_id' => $org->id]);

        $this->assertFalse($registry->isEnabled($org, 'funnels'));

        TenantContext::set($org);
        $middleware = app(EnsureModuleEnabled::class);
        $request = Request::create('/funnels', 'GET');
        $request->setUserResolver(fn () => $user);

        try {
            $middleware->handle($request, fn () => response('ok'), 'funnels');
            $this->fail('Expected module middleware to abort.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }

        $registry->enableForOrganization($org, 'landing_pages');
        $registry->enableForOrganization($org, 'funnels');
        $this->assertTrue($registry->isEnabled($org->fresh(), 'funnels'));

        $response = $middleware->handle($request, fn () => response('ok'), 'funnels');
        $this->assertSame('ok', $response->getContent());
        TenantContext::clear();
    }
}
