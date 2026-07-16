<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('invoices');
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique(); // Ej: 'ACH000058'
            $table->string('movimiento');       // Ej: 'Venta Factura', 'Venta Remisión', 'Pedido Sincronizado'
            $table->date('fecha');
            $table->string('vendedor_id');
            $table->string('zona_id');
            $table->string('customer_id');
            $table->decimal('subtotal', 15, 2);
            $table->decimal('total', 15, 2);
            $table->decimal('abono', 15, 2)->nullable()->default(0.00);
            $table->decimal('saldo', 15, 2)->nullable()->default(0.00);
            $table->string('comentario')->nullable();
            $table->timestamps();

            $table->foreign('vendedor_id')->references('id')->on('sellers')->onDelete('cascade');
            $table->foreign('zona_id')->references('id')->on('zones')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
