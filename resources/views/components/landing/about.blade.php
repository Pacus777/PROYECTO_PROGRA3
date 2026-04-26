<section class="bg-white py-24 md:py-32">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 grid lg:grid-cols-2 gap-12 items-center">
        <div>
            <span class="inline-flex items-center px-4 py-1.5 rounded-full border border-blue-100 bg-blue-50 text-blue-600 text-xs font-semibold tracking-widest uppercase">Sobre el sistema</span>
            <h2 class="mt-5 text-4xl font-bold text-slate-900">Una plataforma diseñada para simplificar la admisión escolar</h2>
            <p class="mt-6 text-base text-slate-600 leading-relaxed">Somos un sistema digital creado para modernizar el proceso de admisión en instituciones educativas. Nuestra plataforma conecta familias, tutores y administradores en un entorno seguro y eficiente.</p>
            <p class="mt-4 text-base text-slate-600 leading-relaxed">Con tecnología de vanguardia y un diseño centrado en el usuario, garantizamos que cada postulación sea tratada con transparencia, equidad y celeridad.</p>
            <a href="#como-funciona" class="inline-flex items-center mt-6 text-blue-600 font-semibold hover:underline">Conoce más sobre el proceso →</a>
        </div>

        <div class="grid grid-cols-2 gap-4">
            @foreach ([['500+','Postulaciones procesadas'],['50+','Instituciones aliadas'],['98%','Tasa de satisfacción'],["< 3'","Tiempo promedio de registro"]] as $metric)
                <article class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                    <p class="text-4xl font-black text-blue-600">{{ $metric[0] }}</p>
                    <p class="text-sm text-slate-500 font-medium mt-1">{{ $metric[1] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
