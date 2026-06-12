<?php

namespace App\Http\Controllers;

use App\Models\Unidade;
use Illuminate\Http\Request;

class UnidadeController extends Controller
{
    public function index() {
        return Unidade::all();
    }

    public function show(Unidade $unidade) {
        return $unidade;
    }
}
