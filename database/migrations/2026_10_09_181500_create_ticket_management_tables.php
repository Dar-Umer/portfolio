<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ticket Categories
        Schema::create('ticket_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // 2. Ticket Statuses (Configurable workflow engine)
        Schema::create('ticket_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Open, Acknowledged, Under Investigation, In Progress, Awaiting Customer Response, Awaiting Developer, Fix Submitted, Ready for Testing, Resolved, Closed, Reopened, Rejected
            $table->string('slug')->unique();
            $table->string('color', 20)->default('#6c757d');
            $table->boolean('is_initial')->default(false);
            $table->boolean('is_resolved')->default(false);
            $table->boolean('is_closed')->default(false);
            $table->json('allowed_transitions')->nullable(); // Array of allowed next status slugs
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // 3. Tickets
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 50)->unique(); // e.g. TKT-2026-000001
            $table->foreignId('client_profile_id')->constrained('client_profiles')->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('ticket_category_id')->nullable()->constrained('ticket_categories')->nullOnDelete();
            $table->foreignId('ticket_status_id')->constrained('ticket_statuses');
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subject');
            $table->longText('description');
            $table->string('priority', 20)->default('Normal'); // Low, Normal, High, Critical
            $table->string('issue_type', 50)->default('Bug Report'); // Bug Report, Feature Request, Technical Support, UI/Design, Performance, Security, Change Request, General Inquiry
            $table->string('environment', 50)->nullable(); // Production, Staging, Local, Mobile
            $table->string('app_version', 50)->nullable();
            $table->string('browser_info')->nullable();
            $table->text('steps_to_reproduce')->nullable();
            $table->text('expected_result')->nullable();
            $table->text('actual_result')->nullable();

            // SLA Tracking
            $table->timestamp('first_response_due_at')->nullable();
            $table->timestamp('first_responded_at')->nullable();
            $table->timestamp('resolution_due_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->boolean('is_sla_breached')->default(false);

            // Resolution details
            $table->text('root_cause')->nullable();
            $table->text('fix_description')->nullable();
            $table->string('fixed_version', 50)->nullable();
            $table->text('customer_instructions')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Ticket Comments / Conversation Thread
        Schema::create('ticket_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->longText('comment');
            $table->boolean('is_internal_note')->default(false); // Strictly hidden from customer
            $table->timestamps();
        });

        // 5. Ticket Attachments
        Schema::create('ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('ticket_comment_id')->nullable()->constrained('ticket_comments')->cascadeOnDelete();
            $table->foreignId('uploaded_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_path'); // Stored on private storage
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size');
            $table->timestamps();
        });

        // 6. Ticket Status Change Histories
        Schema::create('ticket_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('changed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('from_status');
            $table->string('to_status');
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        // 7. Ticket GitHub Links (Commit, PR, Branch, Issue)
        Schema::create('ticket_github_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->string('link_type', 30); // branch, commit, pull_request, issue, release
            $table->string('ref_identifier'); // commit SHA, PR #, branch name, tag
            $table->string('ref_url')->nullable();
            $table->string('title')->nullable();
            $table->string('status', 30)->nullable(); // e.g. merged, open, closed
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_github_links');
        Schema::dropIfExists('ticket_status_histories');
        Schema::dropIfExists('ticket_attachments');
        Schema::dropIfExists('ticket_comments');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('ticket_statuses');
        Schema::dropIfExists('ticket_categories');
    }
};
