<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->increments('id_usuario');

            // Acceso
            $table->string('nombre_completo', 150);
            $table->string('usuario', 50)->unique();
            $table->string('correo', 150)->unique();
            $table->string('contrasena', 255);
            $table->enum('rol', ['administrador', 'pasante', 'becario', 'estudiante', 'docente'])->default('estudiante');
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');

            // Datos personales (todos los roles)
            $table->string('ci', 20)->nullable();
            $table->string('registro_universitario', 30)->nullable();
            $table->string('carrera', 100)->nullable()->default('Ciencias del Desarrollo');
            $table->string('telefono', 20)->nullable();
            $table->string('direccion', 200)->nullable();

            // Según el rol
            $table->string('materia', 150)->nullable();      // solo docente
            $table->string('institucion', 150)->nullable();  // solo pasante / becario
            $table->string('semestre', 20)->nullable();      // solo pasante / becario
            $table->string('curso', 50)->nullable();         // solo pasante / becario

            // Datos de referencia (opcionales)
            $table->string('nombre_referencia', 150)->nullable();
            $table->string('telefono_referencia', 20)->nullable();
            $table->string('direccion_referencia', 200)->nullable();

            $table->timestamp('fecha_registro')->useCurrent();
            $table->dateTime('ultima_conexion')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};