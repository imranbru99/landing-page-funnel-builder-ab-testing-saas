<?php

namespace App\Services\Modules;

use App\Models\Module;
use App\Models\Organization;
use App\Models\OrganizationModule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ModuleRegistry
{
    public function definitions(): array
    {
        return config('modules.definitions', []);
    }

    public function syncDefinitions(): void
    {
        foreach ($this->definitions() as $key => $definition) {
            Module::query()->updateOrCreate(
                ['key' => $key],
                [
                    'name' => $definition['name'],
                    'description' => $definition['description'] ?? null,
                    'category' => $definition['category'] ?? 'core',
                    'is_core' => (bool) ($definition['is_core'] ?? false),
                    'platform_enabled' => (bool) ($definition['platform_enabled'] ?? true),
                    'dependencies' => $definition['dependencies'] ?? [],
                    'default_settings' => $definition['default_settings'] ?? [],
                    'sort_order' => $definition['sort_order'] ?? 0,
                ]
            );
        }
    }

    public function enableForOrganization(Organization $organization, string $moduleKey): OrganizationModule
    {
        $module = Module::query()->where('key', $moduleKey)->firstOrFail();

        if (! $module->platform_enabled && ! $module->is_core) {
            throw new \RuntimeException("Module [{$moduleKey}] is disabled by the platform.");
        }

        foreach ($module->dependencies ?? [] as $dependency) {
            if (! $this->isEnabled($organization, $dependency)) {
                $this->enableForOrganization($organization, $dependency);
            }
        }

        $pivot = OrganizationModule::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'module_id' => $module->id,
            ],
            ['is_enabled' => true]
        );

        $this->forgetCache($organization);

        return $pivot;
    }

    public function disableForOrganization(Organization $organization, string $moduleKey): void
    {
        $module = Module::query()->where('key', $moduleKey)->firstOrFail();

        if ($module->is_core) {
            throw new \RuntimeException("Core module [{$moduleKey}] cannot be disabled.");
        }

        OrganizationModule::query()
            ->where('organization_id', $organization->id)
            ->where('module_id', $module->id)
            ->update(['is_enabled' => false]);

        $this->forgetCache($organization);
    }

    public function isEnabled(Organization $organization, string $moduleKey): bool
    {
        return $this->enabledKeys($organization)->contains($moduleKey);
    }

    public function enabledKeys(Organization $organization): Collection
    {
        return Cache::remember(
            $this->cacheKey($organization),
            now()->addMinutes(10),
            function () use ($organization) {
                $modules = Module::query()->orderBy('sort_order')->get();
                $pivots = OrganizationModule::query()
                    ->where('organization_id', $organization->id)
                    ->get()
                    ->keyBy('module_id');

                return $modules
                    ->filter(function (Module $module) use ($pivots) {
                        if (! $module->platform_enabled && ! $module->is_core) {
                            return false;
                        }

                        if ($module->is_core) {
                            return true;
                        }

                        $pivot = $pivots->get($module->id);

                        return $pivot ? (bool) $pivot->is_enabled : false;
                    })
                    ->pluck('key')
                    ->values();
            }
        );
    }

    public function setPlatformEnabled(string $moduleKey, bool $enabled): Module
    {
        $module = Module::query()->where('key', $moduleKey)->firstOrFail();

        if ($module->is_core && ! $enabled) {
            throw new \RuntimeException('Core modules cannot be disabled at platform level.');
        }

        $module->update(['platform_enabled' => $enabled]);
        Cache::flush();

        return $module->fresh();
    }

    protected function cacheKey(Organization $organization): string
    {
        return "org:{$organization->id}:modules";
    }

    protected function forgetCache(Organization $organization): void
    {
        Cache::forget($this->cacheKey($organization));
    }
}
