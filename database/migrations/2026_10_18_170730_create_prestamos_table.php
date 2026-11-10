<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('prestamos', function (Blueprint $table) {
            $table->increments('id_prestamo');
            $table->unsignedInteger('id_libro');
            $table->unsignedInteger('id_usuario')->nullable();
            $table->unsignedInteger('id_registrado_por');
            $table->enum('origen', ['estudiante', 'presencial'])->default('presencial');
            $table->string('nombre_prestatario', 150)->nullable();
            $table->string('ci_prestatario', 20)->nullable();
            $table->string('carrera_prestatario', 100)->nullable();
            $table->string('telefono_prestatario', 20)->nullable();
            $table->date('fecha_prestamo');
            $table->date('fecha_devolucion_esperada');
            $table->date('fecha_devolucion_real')->nullable();
            $table->enum('estado', ['prestado', 'devuelto', 'atrasado'])->default('prestado');
            $table->string('observaciones', 255)->nullable();

            $table->foreign('id_libro')->references('id_libro')->on('libros');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios');
            $table->foreign('id_registrado_por')->references('id_usuario')->on('usuarios');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestamos');
    }
};