<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'Ciencias Sociales',
            'Psicología',
            'Metodología de la Investigación',
            'Educación',
            'General',
        ] as $nombre) {
            Categoria::firstOrCreate(['nombre' => $nombre]);
        }

        Usuario::updateOrCreate(
            ['usuario' => 'admin'],
            [
                'nombre_completo' => 'Administrador General',
                'correo'          => 'admin@upea.bo',
                'contrasena'      => Hash::make('admin123'),
                'rol'             => 'administrador',
                'estado'          => 'activo',
            ]
        );
    }
}