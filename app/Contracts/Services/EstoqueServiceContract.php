<?php

namespace App\Contracts\Services;

use App\DTOs\Estoque\EstoqueDTO;
use App\Models\Estoque;
use App\Models\Unidade;
use Illuminate\Support\Collection;

interface EstoqueServiceContract
{

    public function addProduto(EstoqueDTO $estoqueDTO): Estoque;

    public function view(Unidade $unidade): Collection;
}
