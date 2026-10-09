<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Settings & Configurations Table
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 50)->default('general')->index();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string'); // string, text, boolean, json, integer
            $table->boolean('is_public')->default(false);
            $table->timestamps();
        });

        // 2. Client Profiles
        Schema::create('client_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('client_number', 50)->unique(); // e.g. CLT-2026-000001
            $table->string('company_name')->nullable();
            $table->string('phone', 50)->nullable();
            $table->text('billing_address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('postal_code', 30)->nullable();
            $table->string('country', 100)->default('India');
            $table->string('tax_number', 50)->nullable(); // GSTIN or Tax Reg No.
            $table->string('client_category', 50)->default('Standard');
            $table->string('account_status', 30)->default('active'); // active, suspended, archived
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Projects
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_profile_id')->constrained('client_profiles')->cascadeOnDelete();
            $table->string('project_number', 50)->unique(); // e.g. PRJ-2026-000001
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category', 50)->default('Web Application');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->date('actual_completion_date')->nullable();
            $table->string('status', 40)->default('Planning'); // Inquiry, Planning, In Development, Testing, Client Review, Completed, On Hold, Cancelled
            $table->string('priority', 20)->default('Normal'); // Low, Normal, High, Critical
            $table->unsignedTinyInteger('progress_percentage')->default(0);
            $table->decimal('budget', 15, 2)->default(0.00);
            $table->json('technologies')->nullable();
            $table->string('current_deployed_version', 50)->nullable();
            $table->text('internal_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Project Milestones
        Schema::create('project_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->string('status', 30)->default('pending'); // pending, in_progress, completed, cancelled
            $table->unsignedTinyInteger('progress')->default(0);
            $table->boolean('is_client_visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // 5. Project Timeline Events
        Schema::create('project_timeline_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_type', 50); // created, milestone_completed, status_changed, release_published, invoice_issued, payment_received
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_client_visible')->default(true);
            $table->timestamps();
        });

        // 6. Project Members (Developers/Staff assigned to project)
        Schema::create('project_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role_in_project', 50)->default('Developer');
            $table->timestamps();
            $table->unique(['project_id', 'user_id']);
        });

        // 7. Project Repositories (GitHub Integration)
        Schema::create('project_repositories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('repo_owner');
            $table->string('repo_name');
            $table->string('default_branch')->default('main');
            $table->string('repo_url');
            $table->text('access_token')->nullable(); // Encrypted
            $table->string('webhook_secret')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->string('last_sync_status', 30)->default('never_synced');
            $table->string('latest_release_tag', 50)->nullable();
            $table->timestamp('latest_release_published_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 8. GitHub Sync Logs
        Schema::create('github_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_repository_id')->constrained('project_repositories')->cascadeOnDelete();
            $table->string('event_type', 50); // manual, webhook, scheduled
            $table->string('status', 30); // success, failed
            $table->text('message')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('github_sync_logs');
        Schema::dropIfExists('project_repositories');
        Schema::dropIfExists('project_members');
        Schema::dropIfExists('project_timeline_events');
        Schema::dropIfExists('project_milestones');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('client_profiles');
        Schema::dropIfExists('settings');
    }
};
