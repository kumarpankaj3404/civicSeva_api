<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Categories ───────────────────────────────────────────────────────
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon', 20)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ─── Schemes ──────────────────────────────────────────────────────────
        Schema::create('schemes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ministry')->nullable();
            $table->json('benefits')->nullable();
            $table->json('eligibility_rules')->nullable();
            $table->json('required_documents')->nullable();
            $table->string('application_url')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('indexed_at')->nullable();
            $table->timestamps();
        });

        // ─── Conversations ────────────────────────────────────────────────────
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('session_id')->unique();
            $table->string('title')->default('New Conversation');
            $table->enum('status', ['active', 'closed', 'archived'])->default('active');
            $table->json('interview_progress')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        // ─── Messages ─────────────────────────────────────────────────────────
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['user', 'assistant', 'system']);
            $table->longText('content');
            $table->json('context_metadata')->nullable();
            $table->timestamps();

            $table->index('conversation_id');
        });

        // ─── Applications ─────────────────────────────────────────────────────
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('scheme_id')->constrained()->cascadeOnDelete();
            $table->foreignId('conversation_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected'])->default('submitted');
            $table->json('interview_data')->nullable();
            $table->text('notes')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('sla_deadline')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('schemes');
        Schema::dropIfExists('categories');
    }
};
