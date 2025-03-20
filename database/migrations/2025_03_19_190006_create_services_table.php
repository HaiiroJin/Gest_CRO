<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('libelle', 250);
            $table->string('libelle_ar', 300)->nullable();
            $table->unsignedBigInteger('division_id')->nullable();
            $table->unsignedBigInteger('direction_id')->nullable();
            $table->foreign('division_id')
                  ->references('id')
                  ->on('divisions')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
            $table->foreign('direction_id')
                  ->references('id')
                  ->on('directions')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
