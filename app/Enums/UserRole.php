<?php

namespace App\Enums;

enum UserRole : string
{
    case ADMIN = 'admin';
    case GERENTE = 'gerente';
    case ATENDENTE = 'atendente';
    case COZINHA = 'cozinha';
    case CLIENTE = 'cliente';
}
