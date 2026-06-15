<?php

namespace App\Contracts\Services;

use App\DTOs\Unidade\UnidadeDTO;
use App\Models\Unidade;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UnidadeServiceContract
{
    public function create(UnidadeDTO $unidadeDTO): Unidade;

    public function list(int $perPage): LengthAwarePaginator;

    public function unidadeAtivo(Unidade $unidade);
}
