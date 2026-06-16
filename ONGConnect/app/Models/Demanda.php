<?php
namespace App\Models;
use App\Enums\StatusDemanda;
use App\Enums\TipoDemanda;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Demanda extends Model
{
    use HasFactory;
    protected $fillable = ['ong_id','titulo','descricao','tipo','status','data_inicio','data_limite','evento_inicio','evento_fim','vagas','endereco','cidade','uf','latitude','longitude'];
    protected function casts(): array {
        return ['tipo' => TipoDemanda::class, 'status' => StatusDemanda::class, 'data_inicio' => 'date', 'data_limite' => 'date', 'evento_inicio' => 'datetime', 'evento_fim' => 'datetime', 'latitude' => 'float', 'longitude' => 'float'];
    }
    public function ong(): BelongsTo { return $this->belongsTo(ONG::class, 'ong_id'); }
    public function categorias(): BelongsToMany { return $this->belongsToMany(Categoria::class, 'categoria_demanda'); }
    public function inscricoes(): HasMany { return $this->hasMany(Inscricao::class); }
    public function vagasDisponiveis(): int {
        if ($this->vagas === null) return PHP_INT_MAX;
        return max(0, $this->vagas - $this->inscricoes()->where('status', 'aceita')->count());
    }
    // ── Evento (data/hora em que a atividade realmente acontece) ──────────────
    // Está ocorrendo agora.
    public function eventoEmAndamento(): bool {
        if (!$this->evento_inicio) return false;
        $fim = $this->evento_fim ?? $this->evento_inicio;
        return $this->evento_inicio->lte(now()) && $fim->gte(now());
    }
    // Ainda vai começar.
    public function eventoFuturo(): bool {
        return $this->evento_inicio !== null && $this->evento_inicio->isFuture();
    }
    // Relevante para a agenda: vai ocorrer ou está ocorrendo agora (não terminou).
    public function eventoAtivo(): bool {
        if (!$this->evento_inicio) return false;
        $fim = $this->evento_fim ?? $this->evento_inicio;
        return $fim->gte(now());
    }
    // Rótulo amigável do período do evento, ex.: "20/06/2026, 14:00–16:00".
    public function getEventoLabelAttribute(): ?string {
        if (!$this->evento_inicio) return null;
        $ini = $this->evento_inicio;
        $fim = $this->evento_fim;
        if (!$fim) return $ini->format('d/m/Y H:i');
        if ($ini->isSameDay($fim)) return $ini->format('d/m/Y').', '.$ini->format('H:i').'–'.$fim->format('H:i');
        return $ini->format('d/m/Y H:i').' até '.$fim->format('d/m/Y H:i');
    }

    public function estaAberta(): bool {
        if ($this->status !== StatusDemanda::Aberta) return false;
        if ($this->data_limite && $this->data_limite->lt(now()->startOfDay())) return false;
        if ($this->data_inicio && $this->data_inicio->gt(now()->startOfDay())) return false;
        return true;
    }

    // Exclui demandas expiradas ou ainda não iniciadas, mesmo sem o scheduler ter rodado
    public function scopeAberta($query) {
        return $query->where('status', StatusDemanda::Aberta->value)
                     ->where(fn ($q) => $q->whereNull('data_limite')
                                          ->orWhere('data_limite', '>=', now()->toDateString()))
                     ->where(fn ($q) => $q->whereNull('data_inicio')
                                          ->orWhere('data_inicio', '<=', now()->toDateString()));
    }
    public function scopeBusca($query, string $termo) { return $query->where(fn($q) => $q->where('titulo','like',"%{$termo}%")->orWhere('descricao','like',"%{$termo}%")); }
}
