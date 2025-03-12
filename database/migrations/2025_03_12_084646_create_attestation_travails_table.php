<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('attestation_travail', function (Blueprint $table) {
        $table->id();
        $table->foreignId('fonctionaire_id')->constrained('fonctionnaires')->onDelete('cascade');
        $table->string('nom');
        $table->string('prenom');
        $table->enum('status', ['en cours', 'signé', 'rejeté'])->default('en cours');
        $table->timestamp('date_demande')->nullable();
        $table->text('demande')->nullable();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attestation_travail');
    }
};
