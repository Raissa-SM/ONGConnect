<?php
namespace App\Models;
use App\Enums\AutorAvaliacao;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Avaliacao extends Model
{
    use HasFactory;
    protected $table = 'avaliacoes';
    protected $fillable = ['inscricao_id','autor_tipo','nota','comentario'];
    protected function casts(): array { return ['autor_tipo' => AutorAvaliacao::class, 'nota' => 'integer']; }
    public function inscricao(): BelongsTo { return $this->belongsTo(Inscricao::class); }
}
