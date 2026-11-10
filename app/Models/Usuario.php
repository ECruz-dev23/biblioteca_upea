<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    protected $fillable = [
        'nombre_completo', 'usuario', 'correo', 'contrasena', 'rol', 'estado',
        'ci', 'registro_universitario', 'carrera', 'telefono', 'direccion',
        'materia', 'institucion', 'semestre', 'curso',
        'nombre_referencia', 'telefono_referencia', 'direccion_referencia',
        'ultima_conexion',
    ];

    protected $hidden = ['contrasena'];

    protected $casts = [
        'ultima_conexion' => 'datetime',
    ];

    // La contraseña está en la columna "contrasena"
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    // No usamos "recordarme"
    public function getRememberTokenName()
    {
        return '';
    }

    // Préstamos/reservas donde esta persona es el prestatario
    public function prestamos(): HasMany
    {
        return $this->hasMany(Prestamo::class, 'id_usuario', 'id_usuario');
    }

    // Préstamos que este usuario registró como personal
    public function prestamosRegistrados(): HasMany
    {
        return $this->hasMany(Prestamo::class, 'id_registrado_por', 'id_usuario');
    }

    public function esAdmin(): bool
    {
        return $this->rol === 'administrador';
    }

    public function esAyudante(): bool
    {
        return in_array($this->rol, ['pasante', 'becario']);
    }

    public function esStaff(): bool
    {
        return $this->esAdmin() || $this->esAyudante();
    }

    // Usuario del catálogo: estudiante o docente (pueden iniciar sesión y reservar)
    public function esEstudiante(): bool
    {
        return in_array($this->rol, ['estudiante', 'docente']);
    }

    // A dónde va cada rol después de iniciar sesión (null = se queda en el catálogo)
    public function rutaPanel(): ?string
    {
        if ($this->esAdmin()) {
            return route('admin.dashboard');
        }
        if ($this->esAyudante()) {
            return route('ayudantes.dashboard');
        }
        return null;
    }
}