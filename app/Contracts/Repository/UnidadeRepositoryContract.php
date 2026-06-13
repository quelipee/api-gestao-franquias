<?php

namespace App\Contracts\Repository;

use App\DTOs\Unidade\UnidadeDTO;
use App\Models\Unidade;

interface UnidadeRepositoryContract
{
    public function save(UnidadeDTO $unidadeDTO): Unidade;
}
