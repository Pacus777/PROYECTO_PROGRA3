<?php

declare(strict_types=1);

namespace App\Services\Ocr;

/**
 * Convierte texto OCR de libreta/boletín en estructura tabular adaptativa.
 * Prueba varios layouts (filas, transpuesto, línea a línea) y elige el que mejor encaja.
 */
final class BoletinLayoutParser
{
    /** @var list<string> */
    private const MATERIAS_CONOCIDAS = [
        'COMUNICACIÓN Y LENGUAJES',
        'LENGUA CASTELLANA',
        'LENGUA EXTRANJERA',
        'CIENCIAS SOCIALES',
        'EDUCACIÓN FÍFICA Y DEPORTES',
        'EDUCACIÓN FÍSICA Y DEPORTES',
        'VALORES, ESPIRITUALIDAD Y RELIGIONES',
        'VALORES ESPIRITUALIDAD Y RELIGIONES',
        'ARTES PLÁSTICAS Y VISUALES',
        'ARTES PLÁSTICAS Y VISUALES Y ESCÉNICAS',
        'MATEMÁTICAS',
        'MATEMÁTICA',
        'CIENCIAS NATURALES',
        'TÉCNICA TECNOLÓGICA GENERAL',
        'TÉCNICA TECNOLÓGICA',
        'TECNICA TECNOLOGICA',
        'EDUCACIÓN MUSICAL',
        'EDUCACION MUSICAL',
        'COSMOVISIONES, FILOSOFÍA Y PSICOLOGÍA',
        'COSMOVISIONES FILOSOFÍA Y PSICOLOGÍA',
        'PRODUCCIÓN Y SERVICIO',
        'FÍSICA',
        'QUÍMICA',
        'BIOLOGÍA',
        'GEOGRAFÍA',
        'HISTORIA',
        'FILOSOFÍA',
        'PSICOLOGÍA',
    ];

    /** @var list<string> */
    private const COLUMNAS_PRIMARIA = ['1T', '2T', '3T', 'PFD', 'PTO', 'PL'];

    /** @var list<string> */
    private const COLUMNAS_SECUNDARIA = ['1T', '2T', 'PFD', 'PTO', 'PL'];

    /**
     * @return array{
     *     titulo: string|null,
     *     encabezado: array<string, string>,
     *     columnas: list<string>,
     *     materias: list<array{nombre: string, notas: list<float|null>}>,
     *     promedio: float|null,
     *     tiene_tabla: bool,
     *     layout_modo: string|null,
     *     confianza_layout: int
     * }
     */
    public function parse(string $text): array
    {
        $text = $this->normalizar($text);
        $encabezado = $this->extraerEncabezado($text);

        $candidatos = [
            $this->estrategiaFilasInline($text),
            $this->estrategiaTranspuesto($text),
            $this->estrategiaPorLineas($text),
        ];

        $mejor = $this->elegirMejorCandidato($candidatos);
        $materias = $mejor['materias'];
        $columnas = $mejor['columnas'];
        $layoutModo = $mejor['modo'];

        if ($columnas === [] && $materias !== []) {
            $columnas = $this->inferirColumnas($materias, $text);
        }

        [$materias, $columnas] = $this->alinearFilas($materias, $columnas);

        $promedio = $this->extraerPromedio($text);
        if ($promedio === null && $materias !== []) {
            $promedio = $this->promedioDesdeMaterias($materias, $columnas);
        }

        return [
            'titulo' => $this->extraerTitulo($text),
            'encabezado' => $encabezado,
            'columnas' => $columnas,
            'materias' => $materias,
            'promedio' => $promedio,
            'tiene_tabla' => $materias !== [],
            'layout_modo' => $layoutModo,
            'confianza_layout' => $mejor['puntuacion'],
        ];
    }

    private function normalizar(string $text): string
    {
        $text = preg_replace('/\r\n|\r/', "\n", $text) ?? $text;
        $text = preg_replace('/[ \t]+/u', ' ', $text) ?? $text;

        foreach (self::MATERIAS_CONOCIDAS as $materia) {
            $text = preg_replace('/(?<!\n)('.preg_quote($materia, '/').')/iu', "\n$1", $text) ?? $text;
        }

        $text = preg_replace('/\n{2,}/', "\n", $text) ?? $text;

        return trim($text);
    }

    /**
     * @param list<array{modo: string, materias: list<array{nombre: string, notas: list<float|null>}>, columnas: list<string>, puntuacion: int}> $candidatos
     * @return array{modo: string, materias: list<array{nombre: string, notas: list<float|null>}>, columnas: list<string>, puntuacion: int}
     */
    private function elegirMejorCandidato(array $candidatos): array
    {
        $mejor = ['modo' => 'ninguno', 'materias' => [], 'columnas' => [], 'puntuacion' => 0];

        foreach ($candidatos as $candidato) {
            if ($candidato['puntuacion'] > $mejor['puntuacion']) {
                $mejor = $candidato;
            }
        }

        return $mejor;
    }

    /**
     * @return array{modo: string, materias: list<array{nombre: string, notas: list<float|null>}>, columnas: list<string>, puntuacion: int}
     */
    private function estrategiaFilasInline(string $text): array
    {
        $materias = $this->extraerMateriasConNotas($text);
        $columnas = $this->extraerEtiquetasColumnas($text);

        if ($columnas === [] && $materias !== []) {
            $columnas = $this->inferirColumnas($materias, $text);
        }

        return [
            'modo' => 'filas',
            'materias' => $materias,
            'columnas' => $columnas,
            'puntuacion' => $this->puntuarResultado($materias, $columnas),
        ];
    }

    /**
     * @return array{modo: string, materias: list<array{nombre: string, notas: list<float|null>}>, columnas: list<string>, puntuacion: int}
     */
    private function estrategiaTranspuesto(string $text): array
    {
        $resultado = $this->extraerFormatoTranspuesto($text);

        return [
            'modo' => 'transpuesto',
            'materias' => $resultado['materias'],
            'columnas' => $resultado['columnas'],
            'puntuacion' => $this->puntuarResultado($resultado['materias'], $resultado['columnas']) + 5,
        ];
    }

    /**
     * Analiza línea por línea: útil cuando el OCR respeta saltos de la foto original.
     *
     * @return array{modo: string, materias: list<array{nombre: string, notas: list<float|null>}>, columnas: list<string>, puntuacion: int}
     */
    private function estrategiaPorLineas(string $text): array
    {
        $lineas = explode("\n", $text);
        $materias = [];
        $filasTrimestre = [];
        $columnas = [];
        $vistos = [];

        foreach ($lineas as $linea) {
            $linea = trim($linea);
            if ($linea === '') {
                continue;
            }

            if (preg_match(
                '/^(I\s*Trim|1\s*Trim|2\s*d?[Oº°0]?\.?\s*Trim(?:estre)?|3\w*\s*Trim|PFD|PTO|\bPL\b)[^\d]{0,40}((?:\d{1,3})(?:\s+\d{1,3}){1,11})/iu',
                $linea,
                $m,
            )) {
                $notas = $this->parseNotas($m[2]);
                if (count($notas) >= 2) {
                    $filasTrimestre[] = [
                        'columna' => $this->normalizarEtiquetaColumna($m[1]),
                        'notas' => $notas,
                    ];
                }

                continue;
            }

            if (preg_match('/^(.+?)\s+((?:\d{1,3}\s+){1,11}\d{1,3})$/u', $linea, $m)) {
                $nombre = $this->limpiarNombreMateria(trim($m[1]));
                if ($nombre === '' || mb_strlen($nombre) < 4 || $this->esEncabezadoFalso($nombre) || ! $this->pareceNombreMateria($nombre)) {
                    continue;
                }

                $notas = $this->parseNotas($m[2]);
                if (count($notas) < 2) {
                    continue;
                }

                $key = mb_strtoupper($nombre);
                if (isset($vistos[$key])) {
                    continue;
                }
                $vistos[$key] = true;

                $materias[] = [
                    'nombre' => $this->formatearNombre($nombre),
                    'notas' => $notas,
                ];
            }
        }

        if ($materias === [] && $filasTrimestre !== []) {
            $corte = $this->indiceInicioNotas($text);
            $bloqueMaterias = mb_substr($text, 0, $corte);
            $nombres = $this->extraerNombresMateriasDelBloque($bloqueMaterias);
            $resultado = $this->construirMatrizTranspuesta($filasTrimestre, $nombres);
            $materias = $resultado['materias'];
            $columnas = $resultado['columnas'];
        }

        if ($columnas === [] && $materias !== []) {
            $columnas = $this->extraerEtiquetasColumnas($text);
            if ($columnas === []) {
                $columnas = $this->inferirColumnas($materias, $text);
            }
        }

        return [
            'modo' => 'lineas',
            'materias' => $materias,
            'columnas' => $columnas,
            'puntuacion' => $this->puntuarResultado($materias, $columnas),
        ];
    }

    /**
     * @param list<array{nombre: string, notas: list<float|null>}> $materias
     * @param list<string> $columnas
     */
    private function puntuarResultado(array $materias, array $columnas): int
    {
        if ($materias === []) {
            return 0;
        }

        $score = count($materias) * 10;

        $conteos = array_map(
            fn (array $m) => count(array_filter($m['notas'], fn ($n) => $n !== null)),
            $materias,
        );

        if ($conteos !== []) {
            $min = min($conteos);
            $max = max($conteos);
            if ($max - $min <= 1) {
                $score += 30;
            } elseif ($max - $min <= 2) {
                $score += 15;
            }

            if ($columnas !== [] && count($columnas) === $max) {
                $score += 20;
            }
        }

        foreach ($materias as $m) {
            if ($this->esMateriaConocida($m['nombre'])) {
                $score += 4;
            }
        }

        return $score;
    }

    private function esMateriaConocida(string $nombre): bool
    {
        $upper = mb_strtoupper($nombre);
        foreach (self::MATERIAS_CONOCIDAS as $m) {
            if (str_contains($upper, mb_strtoupper($m)) || str_contains(mb_strtoupper($m), $upper)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param list<array{nombre: string, notas: list<float|null>}> $materias
     * @param list<string> $columnas
     * @return array{0: list<array{nombre: string, notas: list<float|null>}>, 1: list<string>}
     */
    private function alinearFilas(array $materias, array $columnas): array
    {
        if ($materias === []) {
            return [[], $columnas];
        }

        $maxNotas = max(array_map(fn (array $m) => count($m['notas']), $materias));

        if (count($columnas) < $maxNotas) {
            $faltantes = $maxNotas - count($columnas);
            for ($i = 0; $i < $faltantes; $i++) {
                $columnas[] = 'Col '.(count($columnas) + 1);
            }
        } elseif (count($columnas) > $maxNotas) {
            $columnas = array_slice($columnas, 0, $maxNotas);
        }

        foreach ($materias as &$fila) {
            while (count($fila['notas']) < $maxNotas) {
                $fila['notas'][] = null;
            }
            $fila['notas'] = array_slice($fila['notas'], 0, $maxNotas);
        }
        unset($fila);

        return [$materias, $columnas];
    }

    /**
     * @return list<string>
     */
    private function extraerEtiquetasColumnas(string $text): array
    {
        $etiquetas = [];

        if (preg_match_all(
            '/(I\s*Trim|1\s*Trim|2\s*d?[Oº°]?\.?\s*Trim(?:estre)?|3\w*\s*Trim|PFD|PTO|\bPL\b)/iu',
            $text,
            $matches,
            PREG_OFFSET_CAPTURE,
        )) {
            $posiciones = [];
            foreach ($matches[0] as $m) {
                $etiqueta = $this->normalizarEtiquetaColumna($m[0]);
                $pos = (int) $m[1];
                if (! isset($posiciones[$etiqueta])) {
                    $posiciones[$etiqueta] = $pos;
                }
            }
            asort($posiciones);
            $etiquetas = array_keys($posiciones);
        }

        if (count($etiquetas) >= 2) {
            return $etiquetas;
        }

        foreach (explode("\n", $text) as $linea) {
            if (preg_match('/\d{2,}/', $linea)) {
                continue;
            }
            if (preg_match_all('/\b(1T|2T|3T|PFD|PTO|PL)\b/iu', $linea, $m)) {
                if (count($m[0]) >= 2) {
                    return array_map(fn ($e) => mb_strtoupper($e), $m[0]);
                }
            }
        }

        return [];
    }

    private function indiceInicioNotas(string $text): int
    {
        if (preg_match('/\b(I\s*Trim|1\s*Trim|2\s*d?[Oº°]?\.?\s*Trim|PFD|PTO|\bPL\b)/iu', $text, $m, PREG_OFFSET_CAPTURE)) {
            return (int) $m[0][1];
        }

        return mb_strlen($text);
    }

    /**
     * @return list<string>
     */
    private function extraerNombresMateriasDelBloque(string $bloque): array
    {
        $nombres = [];

        foreach (self::MATERIAS_CONOCIDAS as $nombre) {
            $pos = mb_stripos($bloque, $nombre);
            if ($pos !== false) {
                $nombres[$pos] = $nombre;
            }
        }

        foreach (explode("\n", $bloque) as $linea) {
            $linea = trim($linea);
            if ($linea === '' || preg_match('/\d/', $linea)) {
                continue;
            }
            if (! $this->pareceNombreMateria($linea) || $this->esEncabezadoFalso($linea)) {
                continue;
            }
            $pos = mb_stripos($bloque, $linea);
            if ($pos !== false) {
                $nombres[$pos] = $linea;
            }
        }

        if (preg_match_all(
            '/([A-ZÁÉÍÓÚÑ][A-ZÁÉÍÓÚÑa-záéíóúñ\s,\.\/\-]{4,58})/u',
            preg_replace('/\n/u', ' ', $bloque) ?? $bloque,
            $matches,
        )) {
            foreach ($matches[1] as $candidato) {
                $candidato = trim(preg_replace('/\s{2,}/', ' ', $candidato) ?? $candidato);
                if (! $this->pareceNombreMateria($candidato) || $this->esEncabezadoFalso($candidato)) {
                    continue;
                }
                $pos = mb_stripos($bloque, $candidato);
                if ($pos !== false && ! $this->esSubcadenaDeOtra($candidato, $nombres)) {
                    $nombres[$pos] = $candidato;
                }
            }
        }

        ksort($nombres);

        return array_values(array_unique(array_map(
            fn (string $n) => trim(preg_replace('/\s{2,}/', ' ', $n) ?? $n),
            $nombres,
        )));
    }

    /**
     * @param array<int, string> $existentes
     */
    private function esSubcadenaDeOtra(string $candidato, array $existentes): bool
    {
        $cUpper = mb_strtoupper($candidato);
        foreach ($existentes as $otro) {
            $oUpper = mb_strtoupper($otro);
            if ($cUpper !== $oUpper && (str_contains($oUpper, $cUpper) || str_contains($cUpper, $oUpper))) {
                return mb_strlen($candidato) < mb_strlen($otro);
            }
        }

        return false;
    }

    private function pareceNombreMateria(string $texto): bool
    {
        $texto = trim($texto);
        if (mb_strlen($texto) < 4 || mb_strlen($texto) > 70) {
            return false;
        }

        $letras = preg_match_all('/\p{L}/u', $texto) ?: 0;
        $total = mb_strlen(preg_replace('/\s/u', '', $texto) ?: $texto);

        if ($total === 0 || ($letras / $total) < 0.55) {
            return false;
        }

        return (bool) preg_match('/\p{L}{4,}/u', $texto);
    }

    private function formatearNombre(string $nombre): string
    {
        if (mb_strtoupper($nombre) === $nombre) {
            return mb_convert_case($nombre, MB_CASE_TITLE, 'UTF-8');
        }

        return $nombre;
    }

    private function extraerTitulo(string $text): ?string
    {
        if (preg_match('/(Libreta Escolar Electr[oó]nica[^.\n]{0,120})/iu', $text, $m)) {
            return trim($m[1]);
        }

        if (preg_match('/(Bolet[ií]n[^.\n]{0,80})/iu', $text, $m)) {
            return trim($m[1]);
        }

        return null;
    }

    /**
     * @return array<string, string>
     */
    private function extraerEncabezado(string $text): array
    {
        $campos = [];

        $patrones = [
            'estudiante' => '/(?:Estudiante|Apellidr[_\w]*\s+y)\s*:?\s*([A-ZÁÉÍÓÚÑ][A-ZÁÉÍÓÚÑ\s]{4,80}?)(?:\s+Evaluaci|$)/u',
            'rude' => '/(?:C[oó]digo\s+)?Rude\s*:?\s*([0-9&\s]{8,20})/iu',
            'unidad_educativa' => '/(?:Unid\w*\s+Educativa|Unidad Educativa)\s*:?\s*([A-ZÁÉÍÓÚÑ0-9][^\n]{4,80}?)(?:\s+Dep|\s+Distrito|\s+ELALT|$)/iu',
            'distrito' => '/Distrito\s*(?:Educativo)?\s*:?\s*([^\n]{2,80})/iu',
            'turno' => '/Turno\s*:?\s*([A-ZÁÉÍÓÚÑa-záéíóúñ\s]{2,30})/iu',
            'gestion' => '/G(?:oti[oó]n|esti[oó]n)\s*:?\s*(\d{4})/iu',
            'curso' => '/(?:CASTELLANO|Curso)\s*:?\s*([A-Z0-9\-]{2,20})/iu',
            'paralelo' => '/Paralelo\s*:?\s*([A-Z0-9]{1,3})/iu',
        ];

        foreach ($patrones as $clave => $regex) {
            if (preg_match($regex, $text, $m)) {
                $valor = trim(preg_replace('/\s{2,}/', ' ', $m[1]) ?? $m[1]);
                if ($clave === 'rude') {
                    $valor = preg_replace('/\D/', '', $valor) ?? $valor;
                }
                if ($clave === 'estudiante') {
                    $valor = trim(preg_replace('/\s+Evaluaci.*$/iu', '', $valor) ?? $valor);
                }
                if ($valor !== '') {
                    $campos[$clave] = $valor;
                }
            }
        }

        if (! isset($campos['estudiante']) && preg_match('/\b([A-ZÁÉÍÓÚÑ]{4,}\s+[A-ZÁÉÍÓÚÑ]{4,}\s+[A-ZÁÉÍÓÚÑ]{4,})\b/u', $text, $m)) {
            $campos['estudiante'] = trim($m[1]);
        }

        if (! isset($campos['unidad_educativa']) && preg_match('/\b(REP[UÚ]BLICA[^\n]{0,40}|UE[A-Z\s]{4,40})/iu', $text, $m)) {
            $campos['unidad_educativa'] = trim($m[1]);
        }

        return $campos;
    }

    /**
     * @return list<array{columna: string, notas: list<float|null>}>
     */
    private function extraerFilasTrimestre(string $text): array
    {
        $patronEtiqueta = '/(?:^|\s)(I\s*Trim|1\s*Trim|2\s*d?[Oº°0]?\.?\s*Trim(?:estre)?|3\w*\s*Trim|PFD|PTO|\bPL\b)/iu';

        if (! preg_match_all($patronEtiqueta, $text, $matches, PREG_OFFSET_CAPTURE)) {
            return [];
        }

        $filas = [];
        $total = count($matches[0]);

        for ($i = 0; $i < $total; $i++) {
            $etiqueta = $matches[1][$i][0];
            $inicio = $matches[0][$i][1] + strlen($matches[0][$i][0]);
            $fin = ($i + 1 < $total) ? $matches[0][$i + 1][1] : strlen($text);
            $segmento = substr($text, $inicio, $fin - $inicio);
            $notas = $this->parseNotasAlInicio($segmento);

            if (count($notas) >= 2) {
                $filas[] = [
                    'columna' => $this->normalizarEtiquetaColumna($etiqueta),
                    'notas' => $notas,
                ];
            }
        }

        return $filas;
    }

    /** @return list<float|null> */
    private function parseNotasAlInicio(string $segmento): array
    {
        if (preg_match('/^\s*((?:\d{1,3}\s+)*\d{1,3})(?=\s*(?:\d+[A-Za-zÁÉÍÓÚÑáéíóú]|[^\d\s]|$))/u', trim($segmento), $m)) {
            return $this->parseNotas($m[1]);
        }

        return [];
    }

    /**
     * @param list<array{columna: string, notas: list<float|null>}> $filasTrimestre
     * @param list<string> $nombresMaterias
     * @return array{columnas: list<string>, materias: list<array{nombre: string, notas: list<float|null>}>}
     */
    private function construirMatrizTranspuesta(array $filasTrimestre, array $nombresMaterias): array
    {
        if ($filasTrimestre === [] || $nombresMaterias === []) {
            return ['columnas' => [], 'materias' => []];
        }

        $cantidadMaterias = count($nombresMaterias);
        $columnas = [];

        foreach ($filasTrimestre as &$fila) {
            if (count($fila['notas']) > $cantidadMaterias) {
                $fila['notas'] = array_slice($fila['notas'], 0, $cantidadMaterias);
            }
            $columnas[] = $fila['columna'];
        }
        unset($fila);

        $cantidad = min($cantidadMaterias, max(array_map(fn ($f) => count($f['notas']), $filasTrimestre)));
        $materias = [];

        for ($i = 0; $i < $cantidad; $i++) {
            $notasFila = [];
            foreach ($filasTrimestre as $fila) {
                $notasFila[] = $fila['notas'][$i] ?? null;
            }
            $materias[] = [
                'nombre' => $this->formatearNombre($nombresMaterias[$i]),
                'notas' => $notasFila,
            ];
        }

        return ['columnas' => $columnas, 'materias' => $materias];
    }

    /**
     * @return array{columnas: list<string>, materias: list<array{nombre: string, notas: list<float|null>}>}
     */
    private function extraerFormatoTranspuesto(string $text): array
    {
        $corte = $this->indiceInicioNotas($text);
        $bloqueMaterias = mb_substr($text, 0, $corte);
        $nombresMaterias = $this->extraerNombresMateriasDelBloque($bloqueMaterias);
        $filasTrimestre = $this->extraerFilasTrimestre($text);

        if ($nombresMaterias === [] || $filasTrimestre === []) {
            return ['columnas' => [], 'materias' => []];
        }

        return $this->construirMatrizTranspuesta($filasTrimestre, $nombresMaterias);
    }

    private function normalizarEtiquetaColumna(string $raw): string
    {
        $u = mb_strtoupper(trim($raw));

        if (str_contains($u, 'I TRIM') || (str_contains($u, '1') && str_contains($u, 'TRIM'))) {
            return '1T';
        }
        if (str_contains($u, '2') && str_contains($u, 'TRIM')) {
            return '2T';
        }
        if (str_contains($u, '3') && str_contains($u, 'TRIM')) {
            return '3T';
        }
        if (str_contains($u, 'PFD')) {
            return 'PFD';
        }
        if (str_contains($u, 'PTO')) {
            return 'PTO';
        }
        if ($u === 'PL' || str_contains($u, ' PL')) {
            return 'PL';
        }

        return trim($raw);
    }

    /**
     * @return list<array{nombre: string, notas: list<float|null>}>
     */
    private function extraerMateriasConNotas(string $text): array
    {
        $filas = [];
        $vistos = [];
        $posiciones = [];

        foreach (self::MATERIAS_CONOCIDAS as $materiaCanon) {
            $pattern = '/'.preg_quote($materiaCanon, '/').'\s+((?:\d{1,3}\s+){1,11}\d{1,3})/iu';
            if (preg_match($pattern, $text, $m, PREG_OFFSET_CAPTURE)) {
                $notas = $this->parseNotas($m[1][0]);
                if (count($notas) >= 2) {
                    $key = mb_strtoupper($materiaCanon);
                    if (! isset($vistos[$key])) {
                        $vistos[$key] = true;
                        $posiciones[] = ['pos' => (int) $m[0][1], 'fila' => [
                            'nombre' => $this->formatearNombre($materiaCanon),
                            'notas' => $notas,
                        ]];
                    }
                }
            }
        }

        if (preg_match_all(
            '/([A-ZÁÉÍÓÚÑ][A-ZÁÉÍÓÚÑa-záéíóúñ\s,\.\/\-]{4,58}?)\s+((?:\d{1,3}\s+){1,11}\d{1,3})/u',
            $text,
            $matches,
            PREG_SET_ORDER | PREG_OFFSET_CAPTURE,
        )) {
            foreach ($matches as $m) {
                $nombre = trim(preg_replace('/\s{2,}/', ' ', $m[1][0]) ?? $m[1][0], " \t.-");
                $nombre = $this->limpiarNombreMateria($nombre);

                if ($nombre === '' || mb_strlen($nombre) < 4 || $this->esEncabezadoFalso($nombre) || ! $this->pareceNombreMateria($nombre)) {
                    continue;
                }

                $notas = $this->parseNotas($m[2][0]);
                if (count($notas) < 2) {
                    continue;
                }

                $key = mb_strtoupper($nombre);
                if (isset($vistos[$key])) {
                    continue;
                }
                $vistos[$key] = true;

                $posiciones[] = ['pos' => (int) $m[0][1], 'fila' => [
                    'nombre' => $this->formatearNombre($nombre),
                    'notas' => $notas,
                ]];
            }
        }

        usort($posiciones, fn ($a, $b) => $a['pos'] <=> $b['pos']);

        foreach ($posiciones as $item) {
            $filas[] = $item['fila'];
        }

        return $filas;
    }

    /** @return list<float|null> */
    private function parseNotas(string $bloque): array
    {
        $numeros = preg_split('/\s+/', trim($bloque)) ?: [];
        $notas = [];
        foreach ($numeros as $n) {
            if (! preg_match('/^\d{1,3}$/', $n)) {
                continue;
            }
            $v = (float) $n;
            if ($v >= 0 && $v <= 100) {
                $notas[] = $v;
            }
        }

        return $notas;
    }

    private function limpiarNombreMateria(string $nombre): string
    {
        $nombre = preg_replace('/^(Materia|Area|Área)\s*/iu', '', $nombre) ?? $nombre;
        $nombre = preg_replace('/\s*(1T|2T|3T|PFD|PTO|PL).*$/iu', '', $nombre) ?? $nombre;

        return trim($nombre);
    }

    private function esEncabezadoFalso(string $nombre): bool
    {
        $upper = mb_strtoupper($nombre);

        foreach ([
            'LIBRETA', 'BOLETIN', 'BOLETÍN', 'CODIGO', 'CÓDIGO', 'RUDE', 'UNIDAD', 'DISTRITO', 'TURNO',
            'GESTION', 'GESTIÓN', 'ESTUDIANTE', 'MAÑANA', 'TARDE', 'NOCHE', 'ESTADO', 'PLURINACIONAL',
            'DEPARTAMENTO', 'PARALELO', 'EVALUACI', 'TRIMESTRE', 'TRIM', 'PRIMARIA', 'SECUNDARIA',
        ] as $palabra) {
            if (str_starts_with($upper, $palabra) || $upper === $palabra) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param list<array{nombre: string, notas: list<float|null>}> $materias
     * @return list<string>
     */
    private function inferirColumnas(array $materias, string $text): array
    {
        $detectadas = $this->extraerEtiquetasColumnas($text);
        if ($detectadas !== []) {
            return $detectadas;
        }

        if ($materias === []) {
            return [];
        }

        $max = max(array_map(fn ($f) => count($f['notas']), $materias));

        if ($max >= 6) {
            return self::COLUMNAS_PRIMARIA;
        }

        if ($max === 5) {
            return str_contains(mb_strtoupper($text), '3') && str_contains(mb_strtoupper($text), 'TRIM')
                ? ['1T', '2T', '3T', 'PFD', 'PTO']
                : self::COLUMNAS_SECUNDARIA;
        }

        if ($max === 4) {
            return ['1T', '2T', 'PFD', 'PTO'];
        }

        if ($max === 3) {
            return ['1T', '2T', '3T'];
        }

        return array_map(fn ($i) => 'Col '.$i, range(1, $max));
    }

    private function extraerPromedio(string $text): ?float
    {
        if (preg_match('/promedio\s*(?:general|final)?\s*[:\-]?\s*(\d{1,2}(?:[.,]\d{1,2})?)/iu', $text, $m)) {
            return (float) str_replace(',', '.', $m[1]);
        }

        return null;
    }

    /**
     * @param list<array{nombre: string, notas: list<float|null>}> $materias
     * @param list<string> $columnas
     */
    private function promedioDesdeMaterias(array $materias, array $columnas): ?float
    {
        $notasFinales = [];

        foreach ($materias as $fila) {
            $notas = array_values(array_filter($fila['notas'], fn ($n) => $n !== null));
            $final = null;

            if (in_array('PL', $columnas, true)) {
                $idx = array_search('PL', $columnas, true);
                $final = $idx !== false ? ($notas[$idx] ?? null) : null;
            }

            if ($final === null && count($notas) >= 6) {
                $final = $notas[5];
            } elseif ($final === null && count($notas) >= 5) {
                $final = $notas[4];
            } elseif ($final === null && $notas !== []) {
                $final = $notas[array_key_last($notas)];
            }

            if ($final !== null && $final >= 0 && $final <= 100) {
                $notasFinales[] = $final;
            }
        }

        if ($notasFinales === []) {
            return null;
        }

        return round(array_sum($notasFinales) / count($notasFinales), 2);
    }

    /**
     * @param list<float|null> $notas
     */
    public function notaFinalDeFila(array $notas): ?float
    {
        $notas = array_values(array_filter($notas, fn ($n) => $n !== null));
        if ($notas === []) {
            return null;
        }

        if (count($notas) >= 6) {
            return $notas[5];
        }

        return $notas[array_key_last($notas)];
    }
}
