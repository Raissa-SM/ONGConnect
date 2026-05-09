<?php
namespace App\Enums;
enum TipoDemanda: string
{
    case Presencial = 'presencial';
    case Doacao     = 'doacao';
    case Habilidade = 'habilidade';

    public function label(): string
    {
        return match($this) {
            self::Presencial => 'Voluntariado Presencial',
            self::Doacao     => 'Doação Material',
            self::Habilidade => 'Habilidade Específica',
        };
    }
}
