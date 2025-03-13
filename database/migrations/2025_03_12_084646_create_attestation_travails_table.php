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
    Schema::table('attestation_travail', function (Blueprint $table) {
        $table->string('motif')->nullable();
        $table->enum('parapheur', ['en attente', 'signé'])->default('en attente');
        $table->text('rejection_reason')->nullable();
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
