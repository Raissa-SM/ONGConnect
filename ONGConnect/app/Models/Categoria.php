<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Categoria extends Model
{
    use HasFactory;
    protected $fillable = ['nome', 'slug', 'descricao'];
    protected static function booted(): void {
        static::creating(function (Categoria $c) { if (empty($c->slug)) $c->slug = Str::slug($c->nome); });
    }
    public function voluntarios(): BelongsToMany { return $this->belongsToMany(Voluntario::class, 'categoria_voluntario'); }
    public function demandas(): BelongsToMany { return $this->belongsToMany(Demanda::class, 'categoria_demanda'); }
}
