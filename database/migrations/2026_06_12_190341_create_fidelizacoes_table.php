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
        Schema::create('fidelizacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->integer('pontos_saldo')->default(0);
            $table->integer('pontos_acumulados_total')->default(0);
            $table->integer('pontos_resgatados_total')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::create('transacoes_fidelizacao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fidelizacao_id')
                ->constrained('fidelizacoes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('pedido_id')
                ->nullable()
                ->constrained('pedidos')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('tipo'); //TODO
            $table->integer('pontos');
            $table->string('descricao', 255)->nullable();
            $table->timestamps();

            $table->index('fidelizacao_id');
            $table->index('tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacoes_fidelizacao');
        Schema::dropIfExists('fidelizacoes');
    }
};
