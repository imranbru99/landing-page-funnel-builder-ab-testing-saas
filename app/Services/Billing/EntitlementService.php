<?php

namespace App\Services\Billing;

use App\Models\Organization;
use App\Models\Page;
use App\Models\Funnel;
use App\Models\Contact;
use App\Models\Workspace;
use App\Models\Project;

class EntitlementService
{
    public function plan(Organization $organization)
    {
        return $organization->plan;
    }

    public function allows(Organization $organization, string $feature): bool
    {
        $plan = $this->plan($organization);
        if (! $plan) {
            return false;
        }

        $entitlements = $plan->entitlements ?? [];

        return (bool) ($entitlements[$feature] ?? true);
    }

    public function canCreate(Organization $organization, string $resource): bool
    {
        $plan = $this->plan($organization);
        if (! $plan) {
            return false;
        }

        return match ($resource) {
            'workspace' => Workspace::query()->where('organization_id', $organization->id)->count() < $plan->max_workspaces,
            'project' => Project::query()->where('organization_id', $organization->id)->count() < $plan->max_projects,
            'page' => Page::query()->where('organization_id', $organization->id)->count() < $plan->max_pages,
            'funnel' => Funnel::query()->where('organization_id', $organization->id)->count() < $plan->max_funnels,
            'contact' => Contact::query()->where('organization_id', $organization->id)->count() < $plan->max_contacts,
            default => true,
        };
    }

    public function usage(Organization $organization): array
    {
        $plan = $this->plan($organization);

        return [
            'workspaces' => [
                'used' => Workspace::query()->where('organization_id', $organization->id)->count(),
                'max' => $plan?->max_workspaces,
            ],
            'projects' => [
                'used' => Project::query()->where('organization_id', $organization->id)->count(),
                'max' => $plan?->max_projects,
            ],
            'pages' => [
                'used' => Page::query()->where('organization_id', $organization->id)->count(),
                'max' => $plan?->max_pages,
            ],
            'funnels' => [
                'used' => Funnel::query()->where('organization_id', $organization->id)->count(),
                'max' => $plan?->max_funnels,
            ],
            'contacts' => [
                'used' => Contact::query()->where('organization_id', $organization->id)->count(),
                'max' => $plan?->max_contacts,
            ],
        ];
    }
}
