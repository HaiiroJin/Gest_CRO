<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dossiers_fonctionnaires', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fonctionnaire_id');
            $table->unsignedBigInteger('dossier_id');
            $table->unsignedBigInteger('sous_dossier_id');

            $table->foreign('fonctionnaire_id')
                  ->references('id')
                  ->on('fonctionnaires')
                  ->onDelete('cascade');

            $table->foreign('dossier_id')
                  ->references('id')
                  ->on('dossiers')
                  ->onDelete('cascade');

            $table->foreign('sous_dossier_id')
                  ->references('id')
                  ->on('sous_dossiers')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dossiers_fonctionnaires');
    }
};
