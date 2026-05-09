<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voluntario extends Model
{
    use HasFactory;
    protected $table = 'voluntarios';
    protected $fillable = ['user_id','cpf','telefone','descricao','habilidades','disponibilidade','endereco','cidade','uf','latitude','longitude'];
    protected function casts(): array {
        return ['habilidades' => 'array', 'disponibilidade' => 'array', 'latitude' => 'float', 'longitude' => 'float'];
    }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function categorias(): BelongsToMany { return $this->belongsToMany(Categoria::class, 'categoria_voluntario'); }
    public function inscricoes(): HasMany { return $this->hasMany(Inscricao::class); }
    public function possuiLocalizacao(): bool { return $this->latitude !== null && $this->longitude !== null; }
    public function aptoParaMatch(): bool { return $this->possuiLocalizacao() && $this->categorias()->exists(); }
    public function mediaAvaliacoes(): ?float {
        $avaliacoes = Avaliacao::whereHas('inscricao.voluntario', fn($q) => $q->where('id', $this->id))->where('autor_tipo', 'ong')->get();
        return $avaliacoes->count() >= 3 ? round($avaliacoes->avg('nota'), 2) : null;
    }
}
