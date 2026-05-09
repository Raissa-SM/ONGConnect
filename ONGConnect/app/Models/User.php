<?php

namespace App\Models;

use App\Enums\TipoPerfil;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'tipo_perfil',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'tipo_perfil'       => TipoPerfil::class,
        ];
    }

    public function ong(): HasOne
    {
        return $this->hasOne(ONG::class);
    }

    public function voluntario(): HasOne
    {
        return $this->hasOne(Voluntario::class);
    }

    public function isOng(): bool
    {
        return $this->tipo_perfil === TipoPerfil::ONG;
    }

    public function isVoluntario(): bool
    {
        return $this->tipo_perfil === TipoPerfil::Voluntario;
    }

    public function perfil(): ONG|Voluntario|null
    {
        return $this->isOng() ? $this->ong : $this->voluntario;
    }
}
