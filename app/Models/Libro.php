<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Libro extends Model
{
    protected $table = 'libros';
    protected $primaryKey = 'id_libro';
    public $timestamps = false;

    protected $fillable = [
        'codigo', 'titulo', 'autor', 'editorial', 'anio_publicacion', 'id_categoria',
        'descripcion', 'cantidad_total', 'cantidad_disponible', 'ubicacion',
        'contador_vistas', 'estado',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

    public function prestamos(): HasMany
    {
        return $this->hasMany(Prestamo::class, 'id_libro', 'id_libro');
    }
}