<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_symptoms', function (Blueprint $table) {
            $table->id();

            // untuk seeder: wajib ada
            $table->unsignedBigInteger('case_base_id');
            $table->unsignedBigInteger('symptom_id');

            $table->unsignedTinyInteger('weight')->default(1);

            $table->timestamps();

            $table->unique(['case_base_id', 'symptom_id'], 'case_symptoms_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_symptoms');
    }
};