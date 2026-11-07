<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Domain;
use App\Models\Experiment;
use App\Models\ExperimentVariant;
use App\Models\Form;
use App\Models\Funnel;
use App\Models\FunnelConnection;
use App\Models\FunnelStep;
use App\Models\Organization;
use App\Models\Page;
use App\Models\PageTemplate;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\Plan;
use App\Models\Project;
use App\Models\Subscription;
use App\Models\Tag;
use App\Models\User;
use App\Models\Website;
use App\Models\Workflow;
use App\Models\Workspace;
use App\Services\Modules\ModuleRegistry;
use App\Services\Pages\PagePublisher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $registry = app(ModuleRegistry::class);
        $registry->syncDefinitions();

        $plan = Plan::query()->where('slug', 'growth')->first()
            ?? Plan::query()->first();

        $user = User::query()->updateOrCreate(
            ['email' => 'demo@landingfunnel.test'],
            [
                'name' => 'Demo Owner',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_platform_admin' => false,
            ]
        );

        $org = Organization::query()->updateOrCreate(
            ['slug' => 'acme-growth-studio'],
            [
                'name' => 'Acme Growth Studio',
                'owner_id' => $user->id,
                'plan_id' => $plan?->id,
                'primary_color' => '#0F766E',
                'secondary_color' => '#134E4A',
                'business_email' => 'hello@acme-growth.test',
                'timezone' => 'UTC',
                'locale' => 'en',
                'currency' => 'USD',
                'branding' => [
                    'white_label' => true,
                    'report_logo' => null,
                    'custom_css' => null,
                ],
                'is_active' => true,
                'is_demo' => true,
            ]
        );

        $org->users()->syncWithoutDetaching([
            $user->id => ['role' => 'owner'],
        ]);

        Subscription::query()->updateOrCreate(
            ['organization_id' => $org->id],
            [
                'plan_id' => $plan->id,
                'status' => 'active',
                'trial_ends_at' => null,
                'current_period_starts_at' => now()->startOfMonth(),
                'current_period_ends_at' => now()->endOfMonth(),
            ]
        );

        foreach (array_keys(config('modules.definitions')) as $key) {
            if (in_array($key, ['attendance', 'ai_catalog'], true)) {
                continue;
            }
            try {
                $registry->enableForOrganization($org, $key);
            } catch (\Throwable) {
                // skip platform-disabled modules
            }
        }

        $workspace = Workspace::query()->updateOrCreate(
            ['organization_id' => $org->id, 'slug' => 'primary'],
            ['name' => 'Primary Workspace', 'is_active' => true]
        );

        $project = Project::query()->updateOrCreate(
            ['workspace_id' => $workspace->id, 'slug' => 'growth-site'],
            [
                'organization_id' => $org->id,
                'name' => 'Growth Site',
                'type' => 'website',
                'is_active' => true,
            ]
        );

        $user->update([
            'current_organization_id' => $org->id,
            'current_workspace_id' => $workspace->id,
            'current_project_id' => $project->id,
        ]);

        Domain::query()->updateOrCreate(
            ['host' => 'acme.landingfunnel.test'],
            [
                'organization_id' => $org->id,
                'project_id' => $project->id,
                'type' => 'subdomain',
                'status' => 'verified',
                'ssl_enabled' => true,
                'verified_at' => now(),
                'verification_token' => Str::random(32),
                'dns_instructions' => ['CNAME' => 'pages.landingfunnel.test'],
            ]
        );

        $website = Website::query()->updateOrCreate(
            ['project_id' => $project->id, 'slug' => 'main'],
            [
                'organization_id' => $org->id,
                'name' => 'Acme Main Site',
                'status' => 'published',
                'navigation' => [
                    ['label' => 'Home', 'url' => '/'],
                    ['label' => 'Pricing', 'url' => '/pricing'],
                ],
                'theme' => ['primary' => '#0F766E'],
            ]
        );

        $pipeline = Pipeline::query()->updateOrCreate(
            ['organization_id' => $org->id, 'name' => 'Default Pipeline'],
            ['is_default' => true]
        );

        foreach ([['New', 1], ['Qualified', 2], ['Proposal', 3], ['Won', 4]] as [$name, $pos]) {
            PipelineStage::query()->updateOrCreate(
                ['pipeline_id' => $pipeline->id, 'name' => $name],
                ['organization_id' => $org->id, 'position' => $pos, 'color' => '#0F766E']
            );
        }

        $form = Form::query()->updateOrCreate(
            ['organization_id' => $org->id, 'slug' => 'lead-capture'],
            [
                'project_id' => $project->id,
                'name' => 'Lead Capture',
                'type' => 'form',
                'is_active' => true,
                'fields' => [
                    ['name' => 'name', 'type' => 'text', 'required' => true],
                    ['name' => 'email', 'type' => 'email', 'required' => true],
                    ['name' => 'message', 'type' => 'textarea', 'required' => false],
                ],
            ]
        );

        Workflow::query()->updateOrCreate(
            ['organization_id' => $org->id, 'name' => 'Tag new leads'],
            [
                'status' => 'active',
                'trigger_type' => 'form_submitted',
                'trigger_config' => ['form_id' => $form->id],
                'actions' => [
                    ['type' => 'tag_contact', 'tag' => 'demo-lead'],
                    ['type' => 'update_contact_status', 'status' => 'qualified'],
                    ['type' => 'send_email', 'subject' => 'Welcome to Acme', 'body' => 'Thanks for joining the demo funnel.'],
                ],
            ]
        );

        Tag::query()->firstOrCreate(
            ['organization_id' => $org->id, 'name' => 'demo-lead'],
            ['color' => '#C45C26']
        );

        $template = PageTemplate::query()->first();
        $publisher = app(PagePublisher::class);

        $pageDefs = [
            ['title' => 'Acme Home', 'slug' => 'home', 'sub' => 'acme-home'],
            ['title' => 'Product Tour', 'slug' => 'product-tour', 'sub' => 'acme-tour'],
            ['title' => 'Pricing Offer', 'slug' => 'pricing-offer', 'sub' => 'acme-pricing'],
            ['title' => 'Webinar Signup', 'slug' => 'webinar-signup', 'sub' => 'acme-webinar'],
            ['title' => 'Case Study', 'slug' => 'case-study', 'sub' => 'acme-case'],
        ];

        $pages = [];
        foreach ($pageDefs as $i => $def) {
            $page = Page::query()->updateOrCreate(
                ['project_id' => $project->id, 'slug' => $def['slug']],
                [
                    'organization_id' => $org->id,
                    'website_id' => $website->id,
                    'template_id' => $template?->id,
                    'title' => $def['title'],
                    'status' => 'draft',
                    'sections' => $template?->sections ?: [
                        ['id' => 'h', 'type' => 'hero', 'props' => ['headline' => $def['title'], 'subheadline' => 'Demo page from Acme Growth Studio', 'cta' => 'Book demo', 'cta_url' => '#form']],
                        ['id' => 'f', 'type' => 'features', 'props' => ['title' => 'Why Acme', 'items' => [['title' => 'Speed', 'body' => 'Ship faster'], ['title' => 'Clarity', 'body' => 'Measure lift'], ['title' => 'Scale', 'body' => 'Multi-tenant ready']]]],
                        ['id' => 'form', 'type' => 'form', 'props' => ['title' => 'Capture lead', 'form_slug' => 'lead-capture']],
                    ],
                    'seo' => ['title' => $def['title'], 'description' => 'Demo landing page'],
                    'styles' => ['theme' => 'teal'],
                    'created_by' => $user->id,
                ]
            );

            $publisher->publish($page, $def['slug'], $def['sub'], $user->id);
            $pages[] = $page->fresh();
        }

        // 3 funnels
        for ($i = 1; $i <= 3; $i++) {
            $funnel = Funnel::query()->updateOrCreate(
                ['project_id' => $project->id, 'slug' => 'funnel-'.$i],
                [
                    'organization_id' => $org->id,
                    'name' => 'Demo Funnel '.$i,
                    'status' => 'published',
                    'description' => 'Demo conversion funnel '.$i,
                    'published_at' => now(),
                ]
            );

            $steps = [];
            foreach ([['Landing', 'landing'], ['Lead Form', 'lead_capture'], ['Thank You', 'thank_you']] as $pos => [$name, $type]) {
                $steps[] = FunnelStep::query()->updateOrCreate(
                    ['funnel_id' => $funnel->id, 'slug' => Str::slug($name).'-'.$i],
                    [
                        'organization_id' => $org->id,
                        'page_id' => $pages[$pos]->id ?? $pages[0]->id,
                        'name' => $name,
                        'type' => $type,
                        'position' => $pos + 1,
                        'canvas_x' => 100 + ($pos * 160),
                        'canvas_y' => 120,
                    ]
                );
            }

            FunnelConnection::query()->firstOrCreate([
                'funnel_id' => $funnel->id,
                'from_step_id' => $steps[0]->id,
                'to_step_id' => $steps[1]->id,
            ], ['label' => 'Opt-in', 'condition_type' => 'always']);

            FunnelConnection::query()->firstOrCreate([
                'funnel_id' => $funnel->id,
                'from_step_id' => $steps[1]->id,
                'to_step_id' => $steps[2]->id,
            ], ['label' => 'Submit', 'condition_type' => 'always']);
        }

        // Experiments on first page
        $experiment = Experiment::query()->updateOrCreate(
            ['project_id' => $project->id, 'slug' => 'homepage-headline'],
            [
                'organization_id' => $org->id,
                'page_id' => $pages[0]->id,
                'name' => 'Homepage Headline Test',
                'status' => 'running',
                'goal_event' => 'conversion',
                'hypothesis' => 'A clearer outcome headline increases form submits.',
                'started_at' => now()->subDays(2),
            ]
        );

        ExperimentVariant::query()->updateOrCreate(
            ['experiment_id' => $experiment->id, 'key' => 'A'],
            [
                'organization_id' => $org->id,
                'name' => 'Control',
                'weight' => 50,
                'is_control' => true,
                'content' => ['headline' => 'Grow pipeline with confident experiments', 'cta' => 'Start free'],
            ]
        );

        ExperimentVariant::query()->updateOrCreate(
            ['experiment_id' => $experiment->id, 'key' => 'B'],
            [
                'organization_id' => $org->id,
                'name' => 'Variant B',
                'weight' => 50,
                'is_control' => false,
                'content' => ['headline' => 'Stop guessing. Start converting.', 'cta' => 'See demo'],
            ]
        );

        // 100 contacts
        $stageId = $pipeline->stages()->orderBy('position')->value('id');
        for ($i = 1; $i <= 100; $i++) {
            Contact::query()->updateOrCreate(
                [
                    'organization_id' => $org->id,
                    'email' => sprintf('lead%03d@acme-demo.test', $i),
                ],
                [
                    'project_id' => $project->id,
                    'pipeline_id' => $pipeline->id,
                    'pipeline_stage_id' => $stageId,
                    'first_name' => 'Lead',
                    'last_name' => (string) $i,
                    'company' => 'Acme Prospect '.$i,
                    'source' => $i % 2 === 0 ? 'form:lead-capture' : 'import',
                    'status' => $i % 5 === 0 ? 'qualified' : 'new',
                    'last_activity_at' => now()->subHours($i),
                ]
            );
        }
    }
}
