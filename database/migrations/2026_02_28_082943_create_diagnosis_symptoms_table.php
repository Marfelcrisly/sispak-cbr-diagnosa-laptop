<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnosis_symptoms', function (Blueprint $table) {
            $table->id();

            $table->foreignId('diagnosis_id')
                ->constrained('diagnoses')
                ->cascadeOnDelete();

            $table->foreignId('symptom_id')
                ->constrained('symptoms')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('weight')->default(1);

            $table->timestamps();

            $table->unique(['diagnosis_id', 'symptom_id'], 'diagnosis_symptoms_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnosis_symptoms');
    }
};