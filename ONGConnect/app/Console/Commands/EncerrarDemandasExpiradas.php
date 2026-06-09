<?php

namespace App\Console\Commands;

use App\Enums\StatusDemanda;
use App\Models\Demanda;
use Illuminate\Console\Command;

class EncerrarDemandasExpiradas extends Command
{
    protected $signature   = 'demandas:encerrar-expiradas';
    protected $description = 'Encerra automaticamente demandas abertas com data_limite ultrapassada';

    public function handle(): int
    {
        $count = Demanda::where('status', StatusDemanda::Aberta)
            ->whereNotNull('data_limite')
            ->whereDate('data_limite', '<', now()->toDateString())
            ->update(['status' => StatusDemanda::Encerrada]);

        $this->info("{$count} demanda(s) encerrada(s) automaticamente.");

        return self::SUCCESS;
    }
}
