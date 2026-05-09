<?php
namespace App\Models;
use App\Enums\StatusInscricao;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inscricao extends Model
{
    use HasFactory;
    protected $table = 'inscricoes';
    protected $fillable = ['voluntario_id','demanda_id','status','mensagem','respondida_em','concluida_em'];
    protected function casts(): array {
        return ['status' => StatusInscricao::class, 'respondida_em' => 'datetime', 'concluida_em' => 'datetime'];
    }
    public function voluntario(): BelongsTo { return $this->belongsTo(Voluntario::class); }
    public function demanda(): BelongsTo { return $this->belongsTo(Demanda::class); }
    public function avaliacoes(): HasMany { return $this->hasMany(Avaliacao::class); }
    public function podeAvaliar(): bool { return $this->status === StatusInscricao::Concluida; }
}
