<?php
namespace App\Enums;
enum StatusInscricao: string
{
    case Pendente  = 'pendente';
    case Aceita    = 'aceita';
    case Recusada  = 'recusada';
    case Concluida = 'concluida';
    case Cancelada = 'cancelada';

    public function label(): string
    {
        return match($this) {
            self::Pendente  => 'Pendente',
            self::Aceita    => 'Aceita',
            self::Recusada  => 'Recusada',
            self::Concluida => 'Concluída',
            self::Cancelada => 'Cancelada',
        };
    }
    public function podeAvaliar(): bool { return $this === self::Concluida; }
    public function podeCancelarPeloVoluntario(): bool { return in_array($this, [self::Pendente, self::Aceita]); }
    public function podeResponderPelaOng(): bool { return $this === self::Pendente; }
}
