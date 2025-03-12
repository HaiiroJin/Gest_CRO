<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fonctionnaires', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('nom_ar');
            $table->string('prenom_ar');
            $table->string('cin');
            $table->string('rib');
            $table->foreignId('direction_id')->nullable()->constrained('directions');
            $table->foreignId('division_id')->nullable()->constrained('divisions');
            $table->foreignId('service_id')->nullable()->constrained('services');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fonctionnaires');
    }
};
