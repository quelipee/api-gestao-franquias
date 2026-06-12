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
        Schema::create('estoques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidade_id')
                ->constrained('unidades')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->foreignId('produto_id')
                ->constrained('produtos')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->integer('quantidade')->default(0);
            $table->integer('quantidade_minima')->default(0);
            $table->timestamps();
            $table->unique(['unidade_id', 'produto_id']);
        });

        Schema::create('movimentacoes_estoque', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estoque_id')
                ->constrained('estoques')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('user_id')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('tipo')->default('ENTRADA');
            $table->integer('quantidade');
            $table->string('motivo',255)->nullable();
            $table->timestamps();

            $table->index('estoque_id');
            $table->index('tipo');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimentacoes_estoque');
        Schema::dropIfExists('estoques');
    }
};
