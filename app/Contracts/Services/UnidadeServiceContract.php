<?php

namespace App\Contracts\Services;

use App\DTOs\Unidade\UnidadeDTO;
use App\Models\Unidade;

interface UnidadeServiceContract
{
    public function create(UnidadeDTO $unidadeDTO): Unidade;

    public function list() : array;
}
