<?php

namespace App\Enums;

enum StatusDemanda: string
{
    case Rascunho  = 'rascunho';
    case Aberta    = 'aberta';
    case Encerrada = 'encerrada';
    case Arquivada = 'arquivada';

    public function label(): string
    {
        return match($this) {
            self::Rascunho  => 'Rascunho',
            self::Aberta    => 'Aberta',
            self::Encerrada => 'Encerrada',
            self::Arquivada => 'Arquivada',
        };
    }

    public function podeReceberInscricoes(): bool
    {
        return $this === self::Aberta;
    }
}
