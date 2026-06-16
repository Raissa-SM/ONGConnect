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
    // Classes Tailwind para o selo de status (verde=ok, azul=concluída, amarelo=pendente, vermelho=negativo)
    public function badgeClasses(): string
    {
        return match($this) {
            self::Aceita    => 'bg-green-50 text-green-700',
            self::Concluida => 'bg-blue-50 text-blue-700',
            self::Pendente  => 'bg-amber-50 text-amber-800',
            self::Recusada  => 'bg-red-50 text-danger',
            self::Cancelada => 'bg-red-50 text-danger',
        };
    }
    public function podeAvaliar(): bool { return $this === self::Concluida; }
    public function podeCancelarPeloVoluntario(): bool { return in_array($this, [self::Pendente, self::Aceita]); }
    public function podeResponderPelaOng(): bool { return $this === self::Pendente; }
}
