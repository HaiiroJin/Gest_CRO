<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jours_fériés', function (Blueprint $table) {
            $table->id();
            $table->string('Nom', 50);
            $table->date('Date_départ');
            $table->integer('nombres_jours');
            $table->date('Date_fin');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jours_fériés');
    }
};
