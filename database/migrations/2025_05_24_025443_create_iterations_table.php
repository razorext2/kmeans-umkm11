<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('iterations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('iteration');
            $table->foreignId('umkm_id')->constrained('umkms')->cascadeOnDelete();
            $table->json('distances');
            $table->unsignedTinyInteger('assigned_cluster');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iterations');
    }
};
