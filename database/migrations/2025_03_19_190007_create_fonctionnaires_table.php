<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fonctionnaires', function (Blueprint $table) {
            $table->id();
            $table->string('civilite', 50)->nullable();
            $table->string('nom', 50)->nullable();
            $table->string('prenom', 50)->nullable();
            $table->string('nom_ar', 50)->nullable();
            $table->string('prenom_ar', 50)->nullable();
            $table->string('cin', 20)->nullable()->unique();
            $table->string('rib', 50)->nullable()->unique();
            $table->string('tel')->nullable();
            $table->string('email')->nullable()->unique();
            $table->text('adresse')->nullable();
            $table->date('date_naissance')->nullable();
            $table->date('date_recruitement')->nullable();
            $table->date('date_affectation_cro')->nullable();
            $table->string('poste')->nullable();
            $table->string('situation')->nullable();
            $table->string('matricule_aujour', 50)->nullable()->unique();

            // Foreign keys
            $table->unsignedBigInteger('corps_id')->nullable()->index();
            $table->unsignedBigInteger('grade_id')->nullable()->index();
            $table->unsignedBigInteger('groupe_id')->nullable()->index();
            $table->unsignedBigInteger('direction_id')->nullable()->index();
            $table->unsignedBigInteger('division_id')->nullable()->index();
            $table->unsignedBigInteger('service_id')->nullable()->index();

            // Solde tracking
            $table->integer('solde_année_prec')->nullable();
            $table->integer('solde_année_act')->nullable();

            // Foreign key constraints
            $table->foreign('corps_id')->references('id')->on('corps')->onDelete('set null');
            $table->foreign('grade_id')->references('id')->on('grades')->onDelete('set null');
            $table->foreign('groupe_id')->references('id')->on('groupes')->onDelete('set null');
            $table->foreign('direction_id')->references('id')->on('directions')->onDelete('set null');
            $table->foreign('division_id')->references('id')->on('divisions')->onDelete('set null');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('set null');

            $table->softDeletes(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fonctionnaires');
    }
};
