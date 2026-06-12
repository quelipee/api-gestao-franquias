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
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')
                ->constrained('pedidos')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('forma_pagamento')->default('MOCK');
            $table->string('status')->default('PENDENTE');
            $table->decimal('valor', 10, 2);

            $table->string('gateway_transaction_id',100)->nullable();
            $table->string('gateway_status',50)->nullable();
            $table->json('gateway_payload')->nullable();
            $table->timestamp('aprovado_em')->nullable();
            $table->timestamp('recusado_em')->nullable();
            $table->timestamps();

            $table->index('pedido_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};
