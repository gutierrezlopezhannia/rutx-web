<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('customers');
        Schema::create('customers', function (Blueprint $table) {
            $table->string('id')->primary(); // Ej: '2537 - CLIENTE A CREDITO 01'
            $table->string('clave');         // Ej: '2537'
            $table->string('nombre');        // Ej: 'CLIENTE A CREDITO 01'
            $table->string('direccion')->nullable();
            $table->string('rfc')->nullable();
            $table->string('telefono')->nullable();
            $table->string('plazo')->nullable();
            $table->decimal('limite', 15, 2)->nullable()->default(0.00);
            $table->decimal('saldo', 15, 2)->nullable()->default(0.00);
            $table->string('zona_id');
            $table->timestamps();

            $table->foreign('zona_id')->references('id')->on('zones')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
