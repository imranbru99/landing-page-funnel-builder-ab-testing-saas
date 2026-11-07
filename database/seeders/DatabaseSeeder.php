<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Module;
use App\Models\PageTemplate;
use App\Models\Plan;
use App\Models\User;
use App\Services\Modules\ModuleRegistry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        app(ModuleRegistry::class)->syncDefinitions();

        // Attendance disabled by default at platform level.
        Module::query()->where('key', 'attendance')->update(['platform_enabled' => false]);
        Module::query()->where('key', 'ai_catalog')->update(['platform_enabled' => false]);

        Plan::query()->upsert([
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'billing_interval' => 'monthly',
                'price_cents' => 2900,
                'currency' => 'USD',
                'trial_days' => 14,
                'entitlements' => json_encode(['ab_testing' => true, 'ai_assistant' => false]),
                'module_keys' => json_encode(['landing_pages', 'websites', 'funnels', 'analytics', 'crm', 'forms', 'templates', 'billing']),
                'max_workspaces' => 2,
                'max_projects' => 5,
                'max_pages' => 25,
                'max_funnels' => 10,
                'max_contacts' => 2500,
                'ai_monthly_credits' => 50,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Growth',
                'slug' => 'growth',
                'billing_interval' => 'monthly',
                'price_cents' => 9900,
                'currency' => 'USD',
                'trial_days' => 14,
                'entitlements' => json_encode(['ab_testing' => true, 'ai_assistant' => true, 'automation' => true]),
                'module_keys' => json_encode(['landing_pages', 'websites', 'funnels', 'ab_testing', 'analytics', 'crm', 'forms', 'automation', 'templates', 'ai_assistant', 'domains', 'billing', 'integrations']),
                'max_workspaces' => 10,
                'max_projects' => 40,
                'max_pages' => 200,
                'max_funnels' => 80,
                'max_contacts' => 50000,
                'ai_monthly_credits' => 2000,
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Agency',
                'slug' => 'agency',
                'billing_interval' => 'monthly',
                'price_cents' => 24900,
                'currency' => 'USD',
                'trial_days' => 14,
                'entitlements' => json_encode(['ab_testing' => true, 'ai_assistant' => true, 'automation' => true, 'white_label' => true]),
                'module_keys' => json_encode(array_keys(config('modules.definitions'))),
                'max_workspaces' => 100,
                'max_projects' => 500,
                'max_pages' => 2000,
                'max_funnels' => 500,
                'max_contacts' => 500000,
                'ai_monthly_credits' => 20000,
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ], ['slug'], [
            'name', 'billing_interval', 'price_cents', 'currency', 'trial_days', 'entitlements', 'module_keys',
            'max_workspaces', 'max_projects', 'max_pages', 'max_funnels', 'max_contacts', 'ai_monthly_credits',
            'is_active', 'sort_order', 'updated_at',
        ]);

        User::query()->updateOrCreate(
            ['email' => 'platform@landingfunnel.test'],
            [
                'name' => 'Platform Admin',
                'password' => Hash::make('password'),
                'is_platform_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        $templates = [
            ['name' => 'SaaS Launch', 'category' => 'saas', 'description' => 'Hero, features, pricing, FAQ for product launches.'],
            ['name' => 'Webinar Registration', 'category' => 'lead', 'description' => 'Countdown-friendly webinar capture layout.'],
            ['name' => 'Agency Portfolio', 'category' => 'agency', 'description' => 'Case-study oriented agency landing page.'],
            ['name' => 'Ecommerce Offer', 'category' => 'ecommerce', 'description' => 'Product offer with social proof and urgency CTA.'],
        ];

        foreach ($templates as $template) {
            PageTemplate::query()->updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($template['name'])],
                [
                    'name' => $template['name'],
                    'category' => $template['category'],
                    'description' => $template['description'],
                    'sections' => [
                        ['id' => '1', 'type' => 'hero', 'props' => ['headline' => $template['name'], 'subheadline' => $template['description'], 'cta' => 'Get Started', 'cta_url' => '#']],
                        ['id' => '2', 'type' => 'features', 'props' => ['title' => 'Why it works', 'items' => [['title' => 'Fast', 'body' => 'Launch in minutes'], ['title' => 'Tested', 'body' => 'Experiment ready'], ['title' => 'Modular', 'body' => 'Enable what you need']]]],
                        ['id' => '3', 'type' => 'form', 'props' => ['title' => 'Get the playbook', 'form_slug' => 'lead-capture']],
                    ],
                    'is_marketplace' => true,
                    'is_premium' => false,
                    'installs' => random_int(20, 400),
                ]
            );
        }

        BlogPost::query()->updateOrCreate(
            ['slug' => 'how-to-run-your-first-ab-test'],
            [
                'title' => 'How to run your first A/B test',
                'excerpt' => 'A practical checklist for allocating traffic and reading conversion lift.',
                'body' => "Start with one hypothesis.\nPublish a control page.\nCreate a variant.\nAllocate traffic evenly.\nMeasure the goal event.",
                'status' => 'published',
                'published_at' => now()->subDays(3),
            ]
        );

        BlogPost::query()->updateOrCreate(
            ['slug' => 'modular-saas-for-agencies'],
            [
                'title' => 'Why agencies need modular SaaS',
                'excerpt' => 'Turn features on per client without rebuilding your stack.',
                'body' => 'Module registries let agencies package capabilities by plan and client maturity.',
                'status' => 'published',
                'published_at' => now()->subDay(),
            ]
        );

        $this->call(DemoSeeder::class);
    }
}
