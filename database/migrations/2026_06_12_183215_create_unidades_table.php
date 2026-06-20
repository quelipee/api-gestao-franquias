<?php

use App\Enums\TipoUnidade;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('unidades', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('cnpj', 14)->nullable();
            $table->string('cidade', 100);
            $table->string('estado', 2);
            $table->string('endereco', 255);
            $table->string('telefone', 20)->nullable();
            $table->string('tipo')->default('COMPLETA');
            $table->boolean('ativo')->default(true);
            $table->time('horario_inicio')->nullable();
            $table->time('horario_fim')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('cidade');
            $table->index('ativo');
        });

        Schema::create('unidade_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidade_id')
                ->constrained('unidades')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['unidade_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidade_user');
        Schema::dropIfExists('unidades');
    }
};
