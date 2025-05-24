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
        Schema::create('centroids', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('iteration');
            $table->unsignedTinyInteger('cluster_number');
            $table->double('centroid_modal');
            $table->double('centroid_penghasilan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('centroids');
    }
};
