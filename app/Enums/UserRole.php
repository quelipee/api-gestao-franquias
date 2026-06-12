<?php

namespace App\Enums;

enum UserRole : string
{
    case Admin = 'admin';
    case Gerente = 'gerente';
    case Atendente = 'atendente';
    case Cozinha = 'cozinha';
    case Cliente = 'cliente';
}
