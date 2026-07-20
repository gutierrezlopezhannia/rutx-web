<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->string('id')->primary(); // Ej: '695 - VENDEDOR'
            $table->string('name');
            $table->string('oculto')->nullable()->default('N');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};
