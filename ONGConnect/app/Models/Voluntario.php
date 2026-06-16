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
    // Avaliações que este voluntário recebeu das ONGs, já com a ONG/demanda carregadas.
    public function avaliacoesRecebidas() {
        return Avaliacao::where('autor_tipo', 'ong')
            ->whereHas('inscricao', fn ($q) => $q->where('voluntario_id', $this->id))
            ->with(['inscricao.demanda.ong'])
            ->latest()
            ->get();
    }
    public function mediaAvaliacoes(): ?float {
        $avaliacoes = $this->avaliacoesRecebidas();
        return $avaliacoes->count() >= 3 ? round($avaliacoes->avg('nota'), 2) : null;
    }
    // Trabalhos concluídos (inscrições finalizadas) — bom indicador de experiência.
    public function totalConcluidas(): int {
        return $this->inscricoes()->where('status', 'concluida')->count();
    }

    public function getCpfFormatadoAttribute(): ?string
    {
        if (!$this->cpf) return null;
        $d = preg_replace('/\D/', '', $this->cpf);
        if (strlen($d) !== 11) return $this->cpf;
        return substr($d,0,3).'.'.substr($d,3,3).'.'.substr($d,6,3).'-'.substr($d,9,2);
    }

    public function getTelefoneFormatadoAttribute(): ?string
    {
        if (!$this->telefone) return null;
        $d = preg_replace('/\D/', '', $this->telefone);
        if (strlen($d) === 11) return '('.substr($d,0,2).') '.substr($d,2,5).'-'.substr($d,7,4);
        if (strlen($d) === 10) return '('.substr($d,0,2).') '.substr($d,2,4).'-'.substr($d,6,4);
        return $this->telefone;
    }
}
