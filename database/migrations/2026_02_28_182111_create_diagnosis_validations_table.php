<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('diagnosis_validations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('history_id');     // relasi ke diagnosis_histories.id
            $table->unsignedBigInteger('expert_damage_id')->nullable(); // keputusan pakar (kerusakan)
            $table->unsignedBigInteger('validated_by')->nullable(); // user admin/teknisi yang validasi
            $table->text('note')->nullable();

            $table->timestamp('validated_at')->nullable();
            $table->timestamps();

            $table->foreign('history_id')->references('id')->on('diagnosis_histories')->onDelete('cascade');
            $table->foreign('expert_damage_id')->references('id')->on('damages')->nullOnDelete();
            $table->foreign('validated_by')->references('id')->on('users')->nullOnDelete();

            $table->unique('history_id'); // 1 riwayat hanya divalidasi 1 kali
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnosis_validations');
    }
};