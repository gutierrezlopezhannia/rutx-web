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
        Schema::create('route_profitabilities', function (Blueprint $table) {
            $table->id();
            $table->string('zona_id');
            $table->string('ruta_id');
            $table->date('fecha');
            $table->string('vendedor');
            $table->decimal('ventas_netas', 12, 2)->default(0);
            $table->decimal('ventas_contado', 12, 2)->default(0);
            $table->decimal('ventas_credito', 12, 2)->default(0);
            $table->decimal('preventas', 12, 2)->default(0);
            $table->decimal('costo_compra_preventa', 12, 2)->default(0);
            $table->integer('clientes_preventa')->nullable();
            $table->decimal('entregas', 12, 2)->default(0);
            $table->decimal('costo_producto', 12, 2)->default(0);
            $table->decimal('devolucion_contado', 12, 2)->default(0);
            $table->integer('dias')->default(0);
            $table->decimal('gastos_operativos', 12, 2)->default(0);
            $table->decimal('costo_venta', 12, 2)->default(0);
            $table->decimal('utilidad_ruta', 12, 2)->default(0);
            $table->decimal('porcentaje', 5, 2)->default(0); // e.g. 99.99%
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_profitabilities');
    }
};
