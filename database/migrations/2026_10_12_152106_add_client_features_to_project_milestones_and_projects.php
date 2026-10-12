<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('project_milestones', function (Blueprint $table) {
            $table->text('acceptance_criteria')->nullable()->after('description');
            $table->string('demo_url')->nullable()->after('acceptance_criteria');
            $table->string('deliverable_type', 50)->default('Feature')->after('demo_url'); // Feature, Design, Integration, Bug Fix, Documentation
            $table->timestamp('client_approved_at')->nullable()->after('completion_date');
            $table->text('client_review_note')->nullable()->after('client_approved_at');
            $table->foreignId('requested_by_user_id')->nullable()->after('sort_order')->constrained('users')->nullOnDelete();
        });

        Schema::create('project_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('details');
            $table->string('priority', 20)->default('Medium'); // Low, Medium, High, Critical
            $table->string('status', 30)->default('Submitted'); // Submitted, Under Review, Planned, Completed, Rejected
            $table->foreignId('converted_milestone_id')->nullable()->constrained('project_milestones')->nullOnDelete();
            $table->text('staff_feedback')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_requirements');

        Schema::table('project_milestones', function (Blueprint $table) {
            $table->dropForeign(['requested_by_user_id']);
            $table->dropColumn([
                'acceptance_criteria',
                'demo_url',
                'deliverable_type',
                'client_approved_at',
                'client_review_note',
                'requested_by_user_id',
            ]);
        });
    }
};
