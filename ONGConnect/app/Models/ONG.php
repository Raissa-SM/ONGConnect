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

    public function getCnpjFormatadoAttribute(): ?string
    {
        if (!$this->cnpj) return null;
        $d = preg_replace('/\D/', '', $this->cnpj);
        if (strlen($d) !== 14) return $this->cnpj;
        return substr($d,0,2).'.'.substr($d,2,3).'.'.substr($d,5,3).'/'.substr($d,8,4).'-'.substr($d,12,2);
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
