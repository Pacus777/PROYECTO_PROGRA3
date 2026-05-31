@extends('layouts.dashboard')

@section('title', 'Documento #'.$documento->id_doc.' | OCR')
@section('breadcrumb')
    <span>Panel</span>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('admin.institucional.documentos.index') }}" class="text-indigo-600 hover:underline">Documentos</a>
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="font-medium text-slate-500">#{{ $documento->id_doc }}</span>
@endsection

@section('content')
    @php
        $pos = $documento->postulacion;
        $est = $pos?->estudiante?->persona;
        $nombreEst = $est ? trim(($est->nombres_per ?? '').' '.($est->ap_paterno_per ?? '')) : '—';
        $ocr = $documento->procesamientoOcr;
        $estadoOcr = $ocr?->estado_poc ?? 'pendiente';
        $estadoClasses = [
            'pendiente' => 'bg-amber-50 text-amber-700',
            'procesando' => 'bg-blue-50 text-blue-700',
            'completado' => 'bg-emerald-50 text-emerald-700',
            'error' => 'bg-rose-50 text-rose-700',
            'omitido' => 'bg-slate-100 text-slate-600',
        ];
        $ocrClass = $estadoClasses[$estadoOcr] ?? 'bg-slate-100 text-slate-600';
        $tipoDoc = strtolower((string) ($documento->tipoDocumento->nombre_tdo ?? ''));
        $esBoletin = in_array($tipoDoc, ['boletin', 'libreta_escolar'], true);
        $mostrarVistaBoletin = ($boletinVista['tiene_tabla'] ?? false) || $esBoletin;
    @endphp

    <x-institucional.page
        module="documentos"
        title="Texto transcrito (OCR)"
        subtitle="Revisión del documento «{{ $documento->tipoDocumento->nombre_tdo ?? '—' }}» de {{ $nombreEst }}."
    >
        <div class="mb-4 flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.institucional.documentos.download', $documento) }}"
               class="inline-flex items-center gap-2 rounded-xl bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-900">
                Descargar archivo original
            </a>
            <form method="POST" action="{{ route('admin.institucional.documentos.ocr', $documento) }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-white px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-50">
                    Volver a procesar OCR
                </button>
            </form>
            @if($pos)
                <a href="{{ route('admin.institucional.postulaciones.show', $pos) }}"
                   class="text-sm font-semibold text-indigo-600 hover:underline">
                    Ver postulación #{{ $pos->id_pos }}
                </a>
            @endif
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <x-institucional.panel module="documentos" title="Estado OCR" class="lg:col-span-1">
                <dl class="space-y-3 p-5 text-sm">
                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-400">Estado</dt>
                        <dd class="mt-1">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $ocrClass }}">{{ $estadoOcr }}</span>
                        </dd>
                    </div>
                    @if($ocr?->confianza_poc)
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-400">Confianza estimada</dt>
                            <dd class="mt-1 font-medium text-slate-800">{{ number_format((float) $ocr->confianza_poc, 1) }}%</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-400">Estado documental</dt>
                        <dd class="mt-1 font-medium text-slate-800">{{ $documento->estado_doc ?? 'pendiente' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-400">Tipo</dt>
                        <dd class="mt-1 font-medium text-slate-800">{{ $documento->tipoDocumento->nombre_tdo ?? '—' }}</dd>
                    </div>
                </dl>
            </x-institucional.panel>

            <x-institucional.panel module="documentos" title="Contenido transcrito" class="lg:col-span-2">
                <div class="p-5" x-data="{ tab: '{{ $mostrarVistaBoletin ? 'tabla' : 'texto' }}' }">
                    @if($estadoOcr === 'completado' && $ocr?->texto_extraido_poc)
                        <div class="mb-4 flex flex-wrap gap-2 border-b border-slate-100 pb-3">
                            @if($mostrarVistaBoletin)
                                <button type="button" @click="tab='tabla'"
                                        :class="tab === 'tabla' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                        class="rounded-lg px-4 py-2 text-xs font-semibold transition">
                                    Vista tipo boletín (tabla)
                                </button>
                            @endif
                            <button type="button" @click="tab='texto'"
                                    :class="tab === 'texto' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                    class="rounded-lg px-4 py-2 text-xs font-semibold transition">
                                Texto bruto OCR
                            </button>
                        </div>

                        @if($mostrarVistaBoletin)
                            <div x-show="tab === 'tabla'" x-cloak>
                                <x-institucional.boletin-ocr-layout :boletin="$boletinVista ?? []" />
                            </div>
                        @endif

                        <div x-show="tab === 'texto'" @if($mostrarVistaBoletin) x-cloak @endif>
                            <pre class="max-h-[32rem] overflow-auto whitespace-pre-wrap rounded-xl border border-slate-200 bg-slate-50 p-4 text-xs leading-relaxed text-slate-800">{{ $ocr->texto_extraido_poc }}</pre>
                        </div>

                        <p class="mt-3 text-xs text-slate-500">Revise el contenido antes de verificar el documento. El OCR puede contener errores en nombres o cifras.</p>
                    @elseif(in_array($estadoOcr, ['pendiente', 'procesando'], true))
                        <p class="text-sm text-slate-600">El OCR está en proceso. Si no aparece el texto, ejecute <code class="rounded bg-slate-100 px-1">php artisan queue:work</code> o active <code class="rounded bg-slate-100 px-1">OCR_SYNC=true</code> en el entorno.</p>
                    @else
                        <p class="text-sm text-rose-700">{{ $ocr?->texto_extraido_poc ?? 'No hay texto disponible.' }}</p>
                    @endif
                </div>
            </x-institucional.panel>
        </div>

        @if(($detalleBoletin->isNotEmpty() || $resumenBoletin) && !$mostrarVistaBoletin)
            <x-institucional.panel module="documentos" title="Notas detectadas (boletín)" class="mt-6">
                <div class="p-5">
                    @if($resumenBoletin?->promedio_rbo)
                        <p class="mb-4 text-sm font-semibold text-slate-800">
                            Promedio detectado: <span class="text-indigo-600">{{ number_format((float) $resumenBoletin->promedio_rbo, 2) }}</span>
                        </p>
                    @endif

                    @if($detalleBoletin->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="border-b border-slate-100 bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-400">Materia</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-slate-400">Nota</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($detalleBoletin as $fila)
                                        <tr>
                                            <td class="px-4 py-2 text-slate-800">{{ $fila->materia_dbo }}</td>
                                            <td class="px-4 py-2 text-right font-semibold text-slate-900">{{ number_format((float) $fila->nota_dbo, 1) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p class="mt-3 text-xs text-slate-500">Extracción automática a partir del OCR. Confirme contra el archivo original.</p>
                    @endif
                </div>
            </x-institucional.panel>
        @endif
    </x-institucional.page>
@endsection
