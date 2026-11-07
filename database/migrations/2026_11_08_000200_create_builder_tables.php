<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('websites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('status')->default('draft');
            $table->json('theme')->nullable();
            $table->json('navigation')->nullable();
            $table->json('seo')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['project_id', 'slug']);
        });

        Schema::create('page_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category')->default('landing');
            $table->text('description')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->json('sections')->nullable();
            $table->boolean('is_marketplace')->default(true);
            $table->boolean('is_premium')->default(false);
            $table->unsignedInteger('installs')->default(0);
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('website_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('page_templates')->nullOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->string('status')->default('draft');
            $table->string('publish_path')->nullable();
            $table->string('subdomain')->nullable();
            $table->foreignId('domain_id')->nullable()->constrained()->nullOnDelete();
            $table->json('sections')->nullable();
            $table->json('styles')->nullable();
            $table->json('seo')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['project_id', 'slug']);
            $table->index(['organization_id', 'status']);
            $table->index('subdomain');
            $table->index('publish_path');
        });

        Schema::create('page_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->string('label')->nullable();
            $table->json('sections')->nullable();
            $table->json('styles')->nullable();
            $table->json('seo')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['page_id', 'version']);
        });

        Schema::create('funnels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('status')->default('draft');
            $table->text('description')->nullable();
            $table->json('settings')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['project_id', 'slug']);
        });

        Schema::create('funnel_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funnel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('page_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('type')->default('landing');
            $table->unsignedInteger('position')->default(0);
            $table->integer('canvas_x')->default(0);
            $table->integer('canvas_y')->default(0);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->unique(['funnel_id', 'slug']);
        });

        Schema::create('funnel_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funnel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_step_id')->constrained('funnel_steps')->cascadeOnDelete();
            $table->foreignId('to_step_id')->constrained('funnel_steps')->cascadeOnDelete();
            $table->string('label')->nullable();
            $table->string('condition_type')->default('always');
            $table->json('condition')->nullable();
            $table->timestamps();
        });

        Schema::create('funnel_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funnel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('funnel_step_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('visitor_key');
            $table->string('event')->default('view');
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['funnel_id', 'visitor_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funnel_visits');
        Schema::dropIfExists('funnel_connections');
        Schema::dropIfExists('funnel_steps');
        Schema::dropIfExists('funnels');
        Schema::dropIfExists('page_versions');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('page_templates');
        Schema::dropIfExists('websites');
    }
};
