<?php

namespace App\Support;

use Illuminate\Http\Response;

// Generador de PDF básico sin librerías externas (igual al del prototipo)
class SimplePDF
{
    private $lineasPorPagina = 50;
    private $paginas = [];
    private $paginaActual = [];
    private $contador = 0;

    private function escapar($texto)
    {
        $convertido = @iconv('UTF-8', 'CP1252//TRANSLIT//IGNORE', $texto);
        if ($convertido === false) $convertido = $texto;
        $convertido = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $convertido);
        return $convertido;
    }

    private function agregarLinea($texto, $tamano = 11, $fuente = 'F2')
    {
        $this->paginaActual[] = [$this->escapar($texto), $tamano, $fuente];
        $this->contador++;
        if ($this->contador >= $this->lineasPorPagina) {
            $this->nuevaPagina();
        }
    }

    private function nuevaPagina()
    {
        $this->paginas[] = $this->paginaActual;
        $this->paginaActual = [];
        $this->contador = 0;
    }

    public function titulo($texto)
    {
        $this->agregarLinea($texto, 17, 'F1');
        $this->agregarLinea('', 8, 'F2');
    }

    public function subtitulo($texto)
    {
        $this->agregarLinea($texto, 10, 'F2');
        $this->agregarLinea('', 6, 'F2');
    }

    public function encabezadoTabla($columnas, $anchos)
    {
        $linea = '';
        foreach ($columnas as $i => $col) {
            $linea .= str_pad(mb_substr($col, 0, $anchos[$i]), $anchos[$i] + 2);
        }
        $this->agregarLinea($linea, 10, 'F3');
    }

    public function filaTabla($columnas, $anchos)
    {
        $linea = '';
        foreach ($columnas as $i => $col) {
            $linea .= str_pad(mb_substr((string) $col, 0, $anchos[$i]), $anchos[$i] + 2);
        }
        $this->agregarLinea($linea, 10, 'F4');
    }

    public function espacio()
    {
        $this->agregarLinea('', 8, 'F2');
    }

    // Devuelve la respuesta HTTP de descarga
    public function descargar($nombreArchivo): Response
    {
        if (!empty($this->paginaActual)) $this->nuevaPagina();
        if (empty($this->paginas)) $this->nuevaPagina();

        $objetos = [];
        $objetos[1] = "<< /Type /Catalog /Pages 2 0 R >>";

        $numPaginas = count($this->paginas);
        $kids = [];
        $numObjActual = 7;
        $paginaObjNums = [];
        $contenidoObjNums = [];

        for ($i = 0; $i < $numPaginas; $i++) {
            $paginaObjNums[$i] = $numObjActual;
            $contenidoObjNums[$i] = $numObjActual + 1;
            $kids[] = $numObjActual . " 0 R";
            $numObjActual += 2;
        }

        $objetos[2] = "<< /Type /Pages /Kids [" . implode(" ", $kids) . "] /Count $numPaginas >>";
        $objetos[3] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>";
        $objetos[4] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>";
        $objetos[5] = "<< /Type /Font /Subtype /Type1 /BaseFont /Courier-Bold /Encoding /WinAnsiEncoding >>";
        $objetos[6] = "<< /Type /Font /Subtype /Type1 /BaseFont /Courier /Encoding /WinAnsiEncoding >>";

        foreach ($this->paginas as $i => $lineas) {
            $stream = "BT\n";
            $primera = true;
            foreach ($lineas as $l) {
                list($texto, $tamano, $fuente) = $l;
                $stream .= "/$fuente $tamano Tf\n";
                if ($primera) {
                    $stream .= "50 800 Td\n";
                    $primera = false;
                } else {
                    $stream .= "0 -14 Td\n";
                }
                $stream .= "($texto) Tj\n";
            }
            $stream .= "ET";

            $objetos[$paginaObjNums[$i]] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] "
                . "/Resources << /Font << /F1 3 0 R /F2 4 0 R /F3 5 0 R /F4 6 0 R >> >> "
                . "/Contents " . $contenidoObjNums[$i] . " 0 R >>";

            $objetos[$contenidoObjNums[$i]] = "<< /Length " . strlen($stream) . " >>\nstream\n$stream\nendstream";
        }

        ksort($objetos);

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objetos as $num => $contenido) {
            $offsets[$num] = strlen($pdf);
            $pdf .= "$num 0 obj\n$contenido\nendobj\n";
        }

        $xrefStart = strlen($pdf);
        $totalObjs = max(array_keys($objetos)) + 1;
        $pdf .= "xref\n0 $totalObjs\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i < $totalObjs; $i++) {
            $off = $offsets[$i] ?? 0;
            $pdf .= str_pad($off, 10, "0", STR_PAD_LEFT) . " 00000 n \n";
        }
        $pdf .= "trailer\n<< /Size $totalObjs /Root 1 0 R >>\nstartxref\n$xrefStart\n%%EOF";

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"',
            'Content-Length'      => strlen($pdf),
        ]);
    }
}