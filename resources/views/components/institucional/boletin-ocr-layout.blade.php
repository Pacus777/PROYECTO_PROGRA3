@props(['boletin' => []])

@php
    $titulo = $boletin['titulo'] ?? null;
    $encabezado = $boletin['encabezado'] ?? [];
    $columnas = $boletin['columnas'] ?? [];
    $materias = $boletin['materias'] ?? [];
    $promedio = $boletin['promedio'] ?? null;
    $layoutModo = $boletin['layout_modo'] ?? null;
    $confianza = (int) ($boletin['confianza_layout'] ?? 0);
    $tieneTabla = ($boletin['tiene_tabla'] ?? false) && $materias !== [];

    $modoLabels = [
        'filas' => 'materias en fila (nombre + notas)',
        'transpuesto' => 'materias en bloque y trimestres en filas',
        'lineas' => 'lectura línea por línea del OCR',
    ];
    $modoLabel = $layoutModo ? ($modoLabels[$layoutModo] ?? $layoutModo) : null;

    $labels = [
        'estudiante' => 'Estudiante',
        'rude' => 'Código RUDE',
        'unidad_educativa' => 'Unidad educativa',
        'distrito' => 'Distrito',
        'turno' => 'Turno',
        'gestion' => 'Gestión',
        'curso' => 'Curso',
        'paralelo' => 'Paralelo',
    ];
@endphp

@if(!$tieneTabla)
    <p class="text-sm text-slate-500">No se detectó una tabla de notas en el texto OCR. Use la pestaña «Texto bruto» o reprocese el documento con una foto más nítida.</p>
@else
    <div class="overflow-hidden rounded-2xl border-2 border-slate-200 bg-white shadow-sm">
        {{-- Encabezado tipo libreta --}}
        <div class="border-b border-slate-200 bg-gradient-to-r from-indigo-700 to-indigo-600 px-6 py-5 text-center text-white">
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-indigo-100">Estado Plurinacional de Bolivia</p>
            <h3 class="mt-2 text-lg font-bold leading-snug md:text-xl">
                {{ $titulo ?? 'Libreta escolar / Boletín de notas' }}
            </h3>
        </div>

        @if($encabezado !== [])
            <div class="grid gap-3 border-b border-slate-100 bg-slate-50/80 px-6 py-4 text-sm sm:grid-cols-2">
                @foreach($labels as $key => $label)
                    @if(!empty($encabezado[$key]))
                        <div>
                            <dt class="text-[10px] font-bold uppercase tracking-wide text-slate-400">{{ $label }}</dt>
                            <dd class="mt-0.5 font-semibold text-slate-800">{{ $encabezado[$key] }}</dd>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        {{-- Tabla de calificaciones --}}
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-indigo-50">
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-indigo-900">Materia / Área</th>
                        @foreach($columnas as $col)
                            <th class="px-2 py-3 text-center text-xs font-bold uppercase tracking-wide text-indigo-800">{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($materias as $fila)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-2.5 font-medium text-slate-800">{{ $fila['nombre'] }}</td>
                            @foreach($columnas as $i => $col)
                                @php $nota = $fila['notas'][$i] ?? null; @endphp
                                <td class="px-2 py-2.5 text-center tabular-nums">
                                    @if($nota !== null)
                                        <span @class([
                                            'inline-flex min-w-[2rem] justify-center rounded-md px-1.5 py-0.5 text-xs font-bold',
                                            'bg-emerald-100 text-emerald-800' => $nota >= 51,
                                            'bg-rose-100 text-rose-800' => $nota < 51,
                                        ])>{{ number_format($nota, 0) }}</span>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
                @if($promedio !== null)
                    <tfoot>
                        <tr class="border-t-2 border-indigo-200 bg-indigo-50/50">
                            <td class="px-4 py-3 font-bold text-indigo-900">Promedio general estimado</td>
                            <td class="px-4 py-3 text-center text-lg font-extrabold text-indigo-700" colspan="{{ count($columnas) }}">
                                {{ number_format((float) $promedio, 1) }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <p class="border-t border-slate-100 px-6 py-3 text-[11px] leading-relaxed text-slate-500">
            Vista reconstruida automáticamente según la estructura detectada en <em>esta</em> foto
            @if($modoLabel)
                ({{ $modoLabel }}).
            @else
                .
            @endif
            Columnas y filas se adaptan al número de calificaciones leídas por el OCR; no asume un formato fijo de boletín.
            @if($confianza > 0 && $confianza < 50)
                <span class="font-semibold text-amber-700">Confianza baja — confirme contra el archivo original.</span>
            @endif
        </p>
    </div>
@endif
