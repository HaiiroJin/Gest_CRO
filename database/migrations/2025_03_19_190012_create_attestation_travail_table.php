<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attestations_travail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fonctionnaire_id')->index();
            $table->enum('status', ['en cours', 'signé', 'rejeté', 'au parapheur', 'approuvé'])->default('en cours');
            $table->date('date_demande')->nullable();
            $table->enum('langue', ['fr', 'ar', 'en'])->default('fr');
            $table->text('demande')->nullable();
            $table->text('attestation')->nullable();
            $table->text('raison_rejection')->nullable();

            $table->foreign('fonctionnaire_id')
                  ->references('id')
                  ->on('fonctionnaires')
                  ->onDelete('cascade');

            $table->softDeletes(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attestations_travail');
    }
};
