<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('groupes', function (Blueprint $table) {
            $table->id(); // Creates `id` as bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY
            $table->text('libelle');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('groupes');
    }
};
