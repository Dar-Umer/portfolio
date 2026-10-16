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
        Schema::create('project_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('uploaded_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type')->nullable(); // e.g. pdf, zip, png, docx
            $table->unsignedBigInteger('file_size')->default(0); // in bytes
            $table->string('category')->default('Specification'); // Specification, Design, Contract, Deliverable, Other
            $table->boolean('is_client_visible')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_documents');
    }
};
