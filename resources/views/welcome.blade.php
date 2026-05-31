@extends('layouts.landing')

@section('title', 'AdmisiónEscolar | Plataforma de Admisión Escolar')

@section('content')
    <x-landing.navbar />
    <x-landing.hero />

    <x-landing.colegios-strip :colegios-destacados="$colegiosDestacados" />

    <x-landing.benefits />
    <x-landing.how-it-works />
    <x-landing.about />
    <x-landing.cta-banner />
    <x-landing.footer />

    <x-tutor.assistant-widget context="landing" />
@endsection
