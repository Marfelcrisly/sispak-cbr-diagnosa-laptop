<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnosis_histories', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('selected_symptom_ids');
            $table->unsignedBigInteger('best_case_id')->nullable();
            $table->decimal('best_similarity', 6, 2)->default(0);

            // simpan top 3 untuk transparansi
            $table->json('top_results')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('best_case_id')->references('id')->on('case_bases')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnosis_histories');
    }
};