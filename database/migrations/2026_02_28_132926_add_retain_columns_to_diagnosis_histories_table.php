<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diagnosis_histories', function (Blueprint $table) {

            if (!Schema::hasColumn('diagnosis_histories', 'threshold_used')) {
                $table->decimal('threshold_used', 6, 2)
                      ->default(70.00)
                      ->after('best_similarity');
            }

            if (!Schema::hasColumn('diagnosis_histories', 'needs_review')) {
                $table->boolean('needs_review')
                      ->default(false)
                      ->after('threshold_used');
            }

            if (!Schema::hasColumn('diagnosis_histories', 'pending_case_id')) {
                $table->unsignedBigInteger('pending_case_id')
                      ->nullable()
                      ->after('needs_review');

                $table->foreign('pending_case_id')
                      ->references('id')
                      ->on('pending_cases')
                      ->nullOnDelete();
            }

        });
    }

    public function down(): void
    {
        Schema::table('diagnosis_histories', function (Blueprint $table) {

            if (Schema::hasColumn('diagnosis_histories', 'pending_case_id')) {
                $table->dropForeign(['pending_case_id']);
                $table->dropColumn('pending_case_id');
            }

            if (Schema::hasColumn('diagnosis_histories', 'needs_review')) {
                $table->dropColumn('needs_review');
            }

            if (Schema::hasColumn('diagnosis_histories', 'threshold_used')) {
                $table->dropColumn('threshold_used');
            }

        });
    }
};