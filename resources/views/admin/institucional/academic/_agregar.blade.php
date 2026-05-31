{{-- Panel superior: agregar nivel, curso o paralelo --}}
<section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="flex flex-col gap-4 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-base font-semibold text-slate-900">Agregar elemento</h2>
            <p class="mt-0.5 text-xs text-slate-500">Inicial, Primaria y Secundaria con sus grados ya están en el catálogo. Solo registre los paralelos de su unidad (A, B, C…).</p>
        </div>
        <div class="flex rounded-xl border border-slate-200 bg-slate-50 p-1 sm:min-w-[280px]">
            @foreach([
                ['id' => 'nivel', 'label' => 'Nivel'],
                ['id' => 'curso', 'label' => 'Curso'],
                ['id' => 'paralelo', 'label' => 'Paralelo'],
            ] as $t)
                <button type="button" @click="tab = '{{ $t['id'] }}'"
                        :class="tab === '{{ $t['id'] }}' ? 'bg-white text-indigo-700 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                        class="flex-1 rounded-lg px-3 py-2 text-center text-xs font-semibold transition">
                    {{ $t['label'] }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="p-5">
        <form x-show="tab === 'nivel'" method="POST" action="{{ route('admin.institucional.niveles.store') }}"
              class="flex flex-col gap-4 sm:flex-row sm:items-end">
            @csrf
            <div class="flex-1">
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Nombre del nivel</label>
                <input name="nombre_niv" value="{{ old('nombre_niv') }}" placeholder="Ej. Secundaria comunitaria productiva"
                       class="{{ $inputClass }} @error('nombre_niv') border-rose-400 @enderror">
                @error('nombre_niv')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="shrink-0 rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-200 hover:bg-indigo-700">
                Registrar nivel
            </button>
        </form>

        <form x-show="tab === 'curso'" x-cloak method="POST" action="{{ route('admin.institucional.cursos.store') }}"
              class="flex flex-col gap-4 lg:flex-row lg:items-end">
            @csrf
            <div class="flex-1">
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Nivel</label>
                <select name="id_niv_cur" required class="{{ $selectClass }} @error('id_niv_cur') border-rose-400 @enderror" @disabled($niveles->isEmpty())>
                    <option value="">Seleccione un nivel…</option>
                    @foreach($niveles as $nivel)
                        <option value="{{ $nivel->id_niv }}" @selected(old('id_niv_cur') == $nivel->id_niv)>{{ $nivel->nombre_niv }}</option>
                    @endforeach
                </select>
                @error('id_niv_cur')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                @if($niveles->isEmpty())
                    <p class="mt-2 text-xs text-amber-700">Primero registre al menos un nivel.</p>
                @endif
            </div>
            <div class="flex-1">
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Nombre del curso</label>
                <input name="nombre_cur" value="{{ old('nombre_cur') }}" placeholder="Ej. 1ro de secundaria"
                       class="{{ $inputClass }} @error('nombre_cur') border-rose-400 @enderror" @disabled($niveles->isEmpty())>
                @error('nombre_cur')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="shrink-0 rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-200 hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50" @disabled($niveles->isEmpty())>
                Registrar curso
            </button>
        </form>

        <form x-show="tab === 'paralelo'" x-cloak method="POST" action="{{ route('admin.institucional.paralelos.store') }}"
              class="flex flex-col gap-4 lg:flex-row lg:items-end">
            @csrf
            <div class="flex-1">
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Curso</label>
                <select name="id_cur_par" required class="{{ $selectClass }} @error('id_cur_par') border-rose-400 @enderror" @disabled($cursos->isEmpty())>
                    <option value="">Seleccione un curso…</option>
                    @foreach($cursos as $curso)
                        <option value="{{ $curso->id_cur }}" @selected(old('id_cur_par') == $curso->id_cur)>
                            {{ $curso->nombre_cur }} — {{ $curso->nivel->nombre_niv ?? '' }}
                        </option>
                    @endforeach
                </select>
                @error('id_cur_par')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                @if($cursos->isEmpty())
                    <p class="mt-2 text-xs text-amber-700">Primero registre al menos un curso.</p>
                @endif
            </div>
            <div class="w-full sm:max-w-[140px]">
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Paralelo</label>
                <input name="nombre_par" value="{{ old('nombre_par') }}" placeholder="A, B, C…" maxlength="16"
                       class="{{ $inputClass }} @error('nombre_par') border-rose-400 @enderror" @disabled($cursos->isEmpty())>
                @error('nombre_par')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="shrink-0 rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-200 hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50" @disabled($cursos->isEmpty())>
                Registrar paralelo
            </button>
        </form>
    </div>
</section>
