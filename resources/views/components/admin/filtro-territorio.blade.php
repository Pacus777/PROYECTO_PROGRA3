@props([
    'departamentos',
    'mode' => 'filter',
    'showUnidad' => true,
    'selected' => [],
])

@php
    $sel = array_merge([
        'id_dep' => old('id_dep', request('id_dep', $selected['id_dep'] ?? null)),
        'id_prov' => old('id_prov', request('id_prov', $selected['id_prov'] ?? null)),
        'id_mun' => old('id_mun', request('id_mun', $selected['id_mun'] ?? old('id_mun_ued', $selected['id_mun_ued'] ?? null))),
        'id_dis' => old('id_dis', request('id_dis', $selected['id_dis'] ?? old('id_dis_ued', $selected['id_dis_ued'] ?? null))),
        'id_ued' => old('id_ued', request('id_ued', $selected['id_ued'] ?? null)),
    ], $selected);
    $munName = $mode === 'form' ? 'id_mun_ued' : 'id_mun';
    $disName = $mode === 'form' ? 'id_dis_ued' : 'id_dis';
@endphp

<div
    x-data="adminFiltroTerritorio({
        mode: @js($mode),
        showUnidad: @js($showUnidad),
        selected: @js($sel),
        urls: {
            provincias: @js(route('admin.territorio.provincias')),
            municipios: @js(route('admin.territorio.municipios')),
            distritos: @js(route('admin.territorio.distritos')),
            unidades: @js(route('admin.territorio.unidades')),
        },
        munName: @js($munName),
        disName: @js($disName),
    })"
    class="flex flex-wrap items-end gap-3"
>
    <div class="min-w-[140px]">
        <label class="mb-1 block text-xs font-semibold text-slate-500">Departamento</label>
        <select x-model="id_dep" @change="onDepartamentoChange()"
                @if($mode === 'filter') name="id_dep" @endif
                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm min-w-[140px]">
            <option value="">Todos</option>
            @foreach($departamentos as $dep)
                <option value="{{ $dep->id_dep }}">{{ $dep->nombre_dep }}</option>
            @endforeach
        </select>
    </div>

    <div class="min-w-[140px]">
        <label class="mb-1 block text-xs font-semibold text-slate-500">Provincia</label>
        <select x-model="id_prov" @change="onProvinciaChange()"
                @if($mode === 'filter') name="id_prov" @endif
                :disabled="!id_dep"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm disabled:opacity-50">
            <option value="">Todas</option>
            <template x-for="p in provincias" :key="p.id">
                <option :value="p.id" x-text="p.nombre"></option>
            </template>
        </select>
    </div>

    <div class="min-w-[140px]">
        <label class="mb-1 block text-xs font-semibold text-slate-500">Municipio</label>
        <select x-model="id_mun" @change="onMunicipioChange()"
                :name="munName"
                :disabled="!id_prov"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm disabled:opacity-50">
            <option value="">{{ $mode === 'form' ? 'Seleccione…' : 'Todos' }}</option>
            <template x-for="m in municipios" :key="m.id">
                <option :value="m.id" x-text="m.nombre"></option>
            </template>
        </select>
    </div>

    <div class="min-w-[160px]">
        <label class="mb-1 block text-xs font-semibold text-slate-500">Distrito educativo</label>
        <select x-model="id_dis" @change="onDistritoChange()"
                :name="disName"
                :disabled="!id_dep"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm disabled:opacity-50">
            <option value="">{{ $mode === 'form' ? 'Opcional' : 'Todos' }}</option>
            <template x-for="d in distritos" :key="d.id">
                <option :value="d.id" x-text="d.nombre"></option>
            </template>
        </select>
    </div>

    @if($showUnidad && $mode === 'filter')
        <div class="min-w-[180px] flex-1">
            <label class="mb-1 block text-xs font-semibold text-slate-500">Unidad educativa</label>
            <select name="id_ued" x-model="id_ued"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm">
                <option value="">Todas</option>
                <template x-for="u in unidades" :key="u.id">
                    <option :value="u.id" x-text="u.nombre + (u.codigo ? ' (' + u.codigo + ')' : '')"></option>
                </template>
            </select>
        </div>
    @endif
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('adminFiltroTerritorio', (config) => ({
                    mode: config.mode,
                    showUnidad: config.showUnidad,
                    munName: config.munName,
                    disName: config.disName,
                    id_dep: String(config.selected.id_dep ?? ''),
                    id_prov: String(config.selected.id_prov ?? ''),
                    id_mun: String(config.selected.id_mun ?? ''),
                    id_dis: String(config.selected.id_dis ?? ''),
                    id_ued: String(config.selected.id_ued ?? ''),
                    provincias: [],
                    municipios: [],
                    distritos: [],
                    unidades: [],
                    async init() {
                        if (this.id_dep) {
                            await this.fetchProvincias();
                            await this.fetchDistritos();
                        }
                        if (this.id_prov) {
                            await this.fetchMunicipios();
                        }
                        if (this.showUnidad && this.mode === 'filter') {
                            await this.fetchUnidades();
                        }
                    },
                    async onDepartamentoChange() {
                        this.id_prov = '';
                        this.id_mun = '';
                        this.id_dis = '';
                        this.id_ued = '';
                        this.provincias = [];
                        this.municipios = [];
                        this.distritos = [];
                        this.unidades = [];
                        if (!this.id_dep) return;
                        await this.fetchProvincias();
                        await this.fetchDistritos();
                    },
                    async onProvinciaChange() {
                        this.id_mun = '';
                        this.id_ued = '';
                        this.municipios = [];
                        this.unidades = [];
                        if (!this.id_prov) return;
                        await this.fetchMunicipios();
                    },
                    async onMunicipioChange() {
                        this.id_ued = '';
                        if (this.showUnidad && this.mode === 'filter') {
                            await this.fetchUnidades();
                        }
                    },
                    async onDistritoChange() {
                        this.id_ued = '';
                        if (this.showUnidad && this.mode === 'filter') {
                            await this.fetchUnidades();
                        }
                    },
                    async fetchProvincias() {
                        const res = await fetch(config.urls.provincias + '?id_dep=' + this.id_dep);
                        this.provincias = await res.json();
                    },
                    async fetchMunicipios() {
                        const res = await fetch(config.urls.municipios + '?id_prov=' + this.id_prov);
                        this.municipios = await res.json();
                    },
                    async fetchDistritos() {
                        const res = await fetch(config.urls.distritos + '?id_dep=' + this.id_dep);
                        this.distritos = await res.json();
                    },
                    async fetchUnidades() {
                        const params = new URLSearchParams();
                        if (this.id_mun) params.set('id_mun', this.id_mun);
                        if (this.id_dis) params.set('id_dis', this.id_dis);
                        const res = await fetch(config.urls.unidades + '?' + params.toString());
                        this.unidades = await res.json();
                    },
                }));
            });
        </script>
    @endpush
@endonce
