<?php

namespace App\Enums;

enum CanalPedido: string
{
    case App = 'APP';
    case Totem = 'TOTEM';
    case Balcao = 'BALCAO';
    case Pickup = 'PICKUP';
    case Web = 'WEB';

    public function label(): string
    {
        return match($this) {
            self::App => 'Aplicativo',
            self::Totem => 'Totem de Autoatendimento',
            self::Balcao => 'Balcão',
            self::Pickup => 'Retirada',
            self::Web => 'Site/Web',
        };
    }
}
