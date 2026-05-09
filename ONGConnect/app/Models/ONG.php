<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ONG extends Model
{
    use HasFactory;
    protected $table = 'ongs';
    protected $fillable = ['user_id','razao_social','cnpj','telefone','descricao_missao','endereco','cidade','uf','latitude','longitude'];
    protected function casts(): array { return ['latitude' => 'float', 'longitude' => 'float']; }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function demandas(): HasMany { return $this->hasMany(Demanda::class, 'ong_id'); }
    public function possuiLocalizacao(): bool { return $this->latitude !== null && $this->longitude !== null; }
}
