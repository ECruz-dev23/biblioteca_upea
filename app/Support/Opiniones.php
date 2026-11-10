<?php

namespace App\Support;

class Opiniones
{
    public static function ruta(): string
    {
        return storage_path('app/opiniones.txt');
    }

    public static function guardar(string $origen, ?string $nombre, string $mensaje): bool
    {
        $ruta = self::ruta();
        $carpeta = dirname($ruta);
        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        $fecha = now()->format('d/m/Y H:i');
        $mensajeLimpio = str_replace(["\r\n", "\r", "\n", "|"], [' ', ' ', ' ', ' '], trim($mensaje));
        $nombre = trim((string) $nombre);
        $nombreLimpio = str_replace("|", " ", $nombre !== '' ? $nombre : 'Anónimo');

        $linea = $fecha . "|" . $origen . "|" . $nombreLimpio . "|" . $mensajeLimpio . PHP_EOL;

        return @file_put_contents($ruta, $linea, FILE_APPEND | LOCK_EX) !== false;
    }

    public static function obtener(): array
    {
        $ruta = self::ruta();
        if (!file_exists($ruta)) {
            return [];
        }

        $lineas = file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $opiniones = [];
        foreach ($lineas as $linea) {
            $partes = explode("|", $linea, 4);
            if (count($partes) === 4) {
                $opiniones[] = [
                    'fecha'   => $partes[0],
                    'origen'  => $partes[1],
                    'nombre'  => $partes[2],
                    'mensaje' => $partes[3],
                ];
            }
        }
        return array_reverse($opiniones); // más recientes primero
    }
}