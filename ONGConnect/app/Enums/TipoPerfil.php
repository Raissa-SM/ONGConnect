<?php
namespace App\Enums;
enum TipoPerfil: string
{
    case ONG        = 'ong';
    case Voluntario = 'voluntario';

    public function label(): string
    {
        return match($this) {
            self::ONG        => 'ONG',
            self::Voluntario => 'Voluntário',
        };
    }
}
