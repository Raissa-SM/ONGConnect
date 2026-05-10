<?php

namespace App\Providers;

use App\Models\Avaliacao;
use App\Models\Demanda;
use App\Models\Inscricao;
use App\Models\ONG;
use App\Models\Voluntario;
use App\Policies\AvaliacaoPolicy;
use App\Policies\DemandaPolicy;
use App\Policies\InscricaoPolicy;
use App\Policies\ONGPolicy;
use App\Policies\VoluntarioPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Registro explícito das Policies
        Gate::policy(ONG::class,        ONGPolicy::class);
        Gate::policy(Voluntario::class, VoluntarioPolicy::class);
        Gate::policy(Demanda::class,    DemandaPolicy::class);
        Gate::policy(Inscricao::class,  InscricaoPolicy::class);
        Gate::policy(Avaliacao::class,  AvaliacaoPolicy::class);
    }
}
