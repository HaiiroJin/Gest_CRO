<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sous_dossiers', function (Blueprint $table) {
            $table->id();
            $table->string('nom_sous_doss');
            $table->unsignedBigInteger('id_doss');
            $table->foreign('id_doss')
                  ->references('id')
                  ->on('dossiers')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sous_dossiers');
    }
};
