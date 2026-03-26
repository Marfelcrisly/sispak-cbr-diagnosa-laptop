<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();        // contoh: H1, H2
            $table->string('name');                     // nama kerusakan
            $table->string('category', 50)->nullable(); // hardware/software (optional)
            $table->text('description')->nullable();
            $table->text('solution')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damages');
    }
};