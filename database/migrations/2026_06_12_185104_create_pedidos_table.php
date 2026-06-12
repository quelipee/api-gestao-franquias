<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidade_id')
                ->constrained('unidades')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('user_id')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('numero_pedido', 20)->unique();
            $table->string('canal_pedido')->default('APP');
            $table->string('status')->default('AGUARDANDO_PAGAMENTO');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('desconto', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('observacao', 255)->nullable();
            $table->timestamp('cancelado_em')->nullable();
            $table->string('motivo_cancelamento', 255)->nullable();
            $table->timestamps();

            $table->index('canal_pedido');
            $table->index('status');
            $table->index('unidade_id');
            $table->index('user_id');
            $table->index('created_at');
        });

        Schema::create('itens_pedido', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')
                ->constrained('pedidos')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('produto_id')
                ->constrained('produtos')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->integer('quantidade');
            $table->decimal('preco_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->string('observacao', 255)->nullable();
            $table->timestamps();

            $table->index('pedido_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itens_pedido');
        Schema::dropIfExists('pedidos');
    }
};
