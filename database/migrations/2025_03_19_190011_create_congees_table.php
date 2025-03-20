<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('congés', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fonctionnaire_id')->index();
            $table->enum('status', ['en cours', 'signée', 'rejétée', 'annulée', 'approuvée'])->default('en cours');
            $table->date('date_demande')->default(now());
            $table->date('date_depart');
            $table->date('date_retour');
            $table->integer('nombre_jours');
            $table->enum('type', ['annuel', 'exceptionnel', 'maladie', 'maternité'])->default('annuel');
            $table->boolean('autorisation_sortie_territoire')->default(false);
            $table->text('demande')->nullable();
            $table->text('decision')->nullable();

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
        Schema::dropIfExists('congés');
    }
};
