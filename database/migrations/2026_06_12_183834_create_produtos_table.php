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
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 80);
            $table->string('descricao', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')
                ->constrained('categorias')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('nome', 120);
            $table->string('descricao', 255)->nullable();
            $table->decimal('preco_base', 10, 2);
            $table->boolean('disponivel_periodo_junino')->default(false);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('categoria_id');
            $table->index('ativo');
        });

        Schema::create('cardapio_unidade', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produto_id')
                ->constrained('produtos')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('unidade_id')
                ->constrained('unidades')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->boolean('disponivel')->default(true);
            $table->unique(['produto_id', 'unidade_id']);
            $table->timestamps();

            $table->index('disponivel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cardapio_unidade');
        Schema::dropIfExists('produtos');
        Schema::dropIfExists('categorias');
    }
};
