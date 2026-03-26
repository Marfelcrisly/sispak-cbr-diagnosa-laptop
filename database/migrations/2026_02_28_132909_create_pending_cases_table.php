<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_cases', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable();

            $table->json('selected_symptom_ids');
            $table->unsignedBigInteger('best_case_id')->nullable();
            $table->decimal('best_similarity', 6, 2)->default(0);
            $table->json('top_results')->nullable();

            $table->string('status', 20)->default('pending'); // pending | approved | rejected
            $table->text('review_note')->nullable();

            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->nullOnDelete();

            $table->foreign('best_case_id')
                ->references('id')->on('case_bases')
                ->nullOnDelete();

            $table->foreign('reviewed_by')
                ->references('id')->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_cases');
    }
};