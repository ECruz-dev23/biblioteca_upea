<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('libros', function (Blueprint $table) {
            $table->increments('id_libro');
            $table->string('codigo', 30)->unique();
            $table->string('titulo', 200);
            $table->string('autor', 150);
            $table->string('editorial', 100)->nullable();
            $table->integer('anio_publicacion')->nullable();
            $table->unsignedInteger('id_categoria')->nullable();
            $table->text('descripcion')->nullable();
            $table->integer('cantidad_total')->default(1);
            $table->integer('cantidad_disponible')->default(1);
            $table->string('ubicacion', 50)->nullable();
            $table->integer('contador_vistas')->default(0);
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamp('fecha_registro')->useCurrent();

            $table->foreign('id_categoria')->references('id_categoria')->on('categorias');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('libros');
    }
};