<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('page_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('status')->default('draft');
            $table->string('goal_event')->default('conversion');
            $table->text('hypothesis')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->unique(['project_id', 'slug']);
        });

        Schema::create('experiment_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('experiment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('key');
            $table->unsignedInteger('weight')->default(50);
            $table->boolean('is_control')->default(false);
            $table->json('content')->nullable();
            $table->timestamps();
            $table->unique(['experiment_id', 'key']);
        });

        Schema::create('experiment_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('experiment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->constrained('experiment_variants')->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('visitor_key');
            $table->string('assignment_method')->default('cookie');
            $table->timestamps();
            $table->unique(['experiment_id', 'visitor_key']);
            $table->index(['experiment_id', 'variant_id']);
        });

        Schema::create('experiment_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('experiment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->constrained('experiment_variants')->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('visitor_key');
            $table->string('event_name');
            $table->decimal('value', 12, 2)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['experiment_id', 'event_name']);
            $table->index(['variant_id', 'event_name']);
        });

        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('page_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('funnel_id')->nullable()->constrained()->nullOnDelete();
            $table->string('visitor_key')->nullable();
            $table->string('session_key')->nullable();
            $table->string('event_name');
            $table->string('url')->nullable();
            $table->string('referrer')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->json('properties')->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamps();
            $table->index(['organization_id', 'event_name', 'occurred_at']);
            $table->index(['page_id', 'event_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
        Schema::dropIfExists('experiment_events');
        Schema::dropIfExists('experiment_assignments');
        Schema::dropIfExists('experiment_variants');
        Schema::dropIfExists('experiments');
    }
};
