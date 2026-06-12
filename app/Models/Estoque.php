<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estoque extends Model
{
    public function unidade()
    {
        return $this->belongsTo(Unidade::class);
    }
}
