<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id(); // Creates `id` as bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY
            $table->string('libelle', 250);
            $table->string('libelle_ar', 250);
            
            // Foreign key to corps table
            $table->unsignedBigInteger('cor_id')->nullable();
            $table->foreign('cor_id')
                  ->references('id')
                  ->on('corps')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
