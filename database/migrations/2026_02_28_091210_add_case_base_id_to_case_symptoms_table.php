<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('case_symptoms', function (Blueprint $table) {

            if (!Schema::hasColumn('case_symptoms', 'case_base_id')) {
                $table->unsignedBigInteger('case_base_id')->after('id');
            }

            if (!Schema::hasColumn('case_symptoms', 'symptom_id')) {
                $table->unsignedBigInteger('symptom_id')->after('case_base_id');
            }

            if (!Schema::hasColumn('case_symptoms', 'weight')) {
                $table->unsignedTinyInteger('weight')->default(1);
            }
        });

        // pasang FK terpisah biar aman setelah kolom ada
        Schema::table('case_symptoms', function (Blueprint $table) {

            // FK ke case_bases
            $table->foreign('case_base_id', 'case_symptoms_case_base_fk')
                ->references('id')->on('case_bases')
                ->cascadeOnDelete();

            // FK ke symptoms
            $table->foreign('symptom_id', 'case_symptoms_symptom_fk')
                ->references('id')->on('symptoms')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('case_symptoms', function (Blueprint $table) {

            // drop FK dulu
            $table->dropForeign('case_symptoms_case_base_fk');
            $table->dropForeign('case_symptoms_symptom_fk');

            // kolom jangan dihapus kalau kamu mau, tapi aku kasih lengkap:
            // kalau mau aman, boleh hapus 2 baris dropColumn ini.
            if (Schema::hasColumn('case_symptoms', 'case_base_id')) {
                $table->dropColumn('case_base_id');
            }
            if (Schema::hasColumn('case_symptoms', 'symptom_id')) {
                $table->dropColumn('symptom_id');
            }
        });
    }
};