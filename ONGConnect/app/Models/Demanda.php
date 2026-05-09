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
    protected $fillable = ['ong_id','titulo','descricao','tipo','status','data_inicio','data_limite','vagas','endereco','cidade','uf','latitude','longitude'];
    protected function casts(): array {
        return ['tipo' => TipoDemanda::class, 'status' => StatusDemanda::class, 'data_inicio' => 'date', 'data_limite' => 'date', 'latitude' => 'float', 'longitude' => 'float'];
    }
    public function ong(): BelongsTo { return $this->belongsTo(ONG::class, 'ong_id'); }
    public function categorias(): BelongsToMany { return $this->belongsToMany(Categoria::class, 'categoria_demanda'); }
    public function inscricoes(): HasMany { return $this->hasMany(Inscricao::class); }
    public function vagasDisponiveis(): int { return max(0, $this->vagas - $this->inscricoes()->where('status', 'aceita')->count()); }
    public function estaAberta(): bool { return $this->status === StatusDemanda::Aberta; }
    public function scopeAberta($query) { return $query->where('status', StatusDemanda::Aberta->value); }
    public function scopeBusca($query, string $termo) { return $query->where(fn($q) => $q->where('titulo','like',"%{$termo}%")->orWhere('descricao','like',"%{$termo}%")); }
}
