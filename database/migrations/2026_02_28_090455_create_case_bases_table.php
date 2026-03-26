<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_bases', function (Blueprint $table) {
            $table->id();

            // SESUAI SEEDER: case_code
            $table->string('case_code', 20)->unique(); // contoh: C001

            // SESUAI SEEDER: damage_id
            $table->foreignId('damage_id')
                ->constrained('damages')
                ->cascadeOnDelete();

            // SESUAI SEEDER: note
            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_bases');
    }
};