@extends('layouts.landing')

@section('title', 'AdmisiónEscolar | Plataforma de Admisión Escolar')

@section('content')
    <x-landing.navbar />
    <x-landing.hero />

    <section class="bg-white border-y border-slate-100 py-10">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <p class="text-sm text-slate-400 font-medium tracking-wide uppercase text-center mb-8">
                Instituciones educativas que confían en nosotros
            </p>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 items-center text-center">
                @foreach (['Colegio San Marcos', 'IE Libertad', 'Centro Educativo Norte', 'Instituto Horizonte', 'Unidad Educativa Sur'] as $logo)
                    <div class="text-slate-300 font-bold text-xl tracking-tight opacity-50 grayscale hover:opacity-100 hover:grayscale-0 transition-all duration-300">
                        {{ $logo }}
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <x-landing.benefits />
    <x-landing.how-it-works />
    <x-landing.about />
    <x-landing.cta-banner />
    <x-landing.footer />
@endsection
