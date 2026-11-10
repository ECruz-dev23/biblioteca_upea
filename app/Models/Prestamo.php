<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prestamo extends Model
{
    protected $table = 'prestamos';
    protected $primaryKey = 'id_prestamo';
    public $timestamps = false;

    protected $fillable = [
        'id_libro', 'id_usuario', 'id_registrado_por', 'origen', 'nombre_prestatario',
        'ci_prestatario', 'carrera_prestatario', 'telefono_prestatario', 'fecha_prestamo',
        'fecha_devolucion_esperada', 'fecha_devolucion_real', 'estado', 'observaciones',
    ];

    public function libro(): BelongsTo
    {
        return $this->belongsTo(Libro::class, 'id_libro', 'id_libro');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_registrado_por', 'id_usuario');
    }

    // Nombre del prestatario: cuenta del estudiante o datos del préstamo presencial
    public function getPrestatarioNombreAttribute(): ?string
    {
        return $this->usuario?->nombre_completo ?? $this->nombre_prestatario;
    }

    public function getPrestatarioCiAttribute(): ?string
    {
        return $this->usuario?->ci ?? $this->ci_prestatario;
    }

    // Marca como "atrasado" los préstamos vencidos
    public static function marcarAtrasados(): void
    {
        static::where('estado', 'prestado')
            ->where('fecha_devolucion_esperada', '<', now()->toDateString())
            ->update(['estado' => 'atrasado']);
    }
}