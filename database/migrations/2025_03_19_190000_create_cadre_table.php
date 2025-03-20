<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cadres', function (Blueprint $table) {
            $table->id(); // This creates `id` as bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY
            $table->string('libelle', 100)->nullable();
            $table->string('libelle_ar', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cadres');
    }
};
