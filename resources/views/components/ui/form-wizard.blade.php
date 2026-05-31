@props([
    'steps' => [],
    'action',
    'method' => 'POST',
    'submitLabel' => 'Guardar',
    'cancelUrl' => null,
    'modal' => null,
])

@php
    $stepLabels = array_values($steps);
    $stepCount = count($stepLabels);
    $httpMethod = strtoupper($method);
    $wizardConfig = [
        'totalSteps' => max(1, $stepCount),
        'steps' => $stepLabels,
    ];
@endphp

<form
    method="{{ $httpMethod === 'GET' ? 'GET' : 'POST' }}"
    action="{{ $action }}"
    class="space-y-6"
    x-data="formWizard(@js($wizardConfig))"
    x-on:submit="onSubmit($event)"
>
    @csrf
    @if(! in_array($httpMethod, ['GET', 'POST'], true))
        @method($httpMethod)
    @endif
    @if($modal)
        <input type="hidden" name="_modal" value="{{ $modal }}">
    @endif

    @if($stepCount > 1)
        <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-4">
            <div class="mb-3 flex items-center justify-between gap-2 text-xs text-slate-500">
                <span>Paso <span class="font-bold text-indigo-600" x-text="currentStep + 1"></span> de <span x-text="totalSteps"></span></span>
                <span class="font-semibold text-indigo-600" x-text="Math.round(progress()) + '%'"></span>
            </div>
            <div class="h-2 overflow-hidden rounded-full bg-slate-200">
                <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 transition-all duration-300" :style="'width:' + progress() + '%'"></div>
            </div>
            <ol class="mt-4 flex flex-wrap gap-2">
                <template x-for="(label, i) in steps" :key="i">
                    <li>
                        <button
                            type="button"
                            x-on:click="goTo(i)"
                            class="rounded-full px-3 py-1 text-xs font-semibold transition"
                            :class="i === currentStep
                                ? 'bg-indigo-600 text-white shadow-sm'
                                : (i < currentStep ? 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200' : 'bg-white text-slate-500 ring-1 ring-slate-200')"
                            x-text="label"
                        ></button>
                    </li>
                </template>
            </ol>
        </div>
    @endif

    <div class="min-h-[12rem]">
        {{ $slot }}
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-5">
        <div>
            @if($cancelUrl && ! $modal)
                <a href="{{ $cancelUrl }}" class="text-sm font-medium text-slate-500 hover:text-slate-800">Cancelar</a>
            @elseif($modal)
                <button type="button" class="text-sm font-medium text-slate-500 hover:text-slate-800"
                        onclick="window.cerrarModal(@js($modal))">
                    Cancelar
                </button>
            @endif
        </div>

        <div class="flex flex-wrap gap-2">
            <button type="button" x-show="currentStep > 0" x-on:click="prev()"
                    class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Anterior
            </button>
            <button type="button" x-show="currentStep < totalSteps - 1" x-on:click="next()"
                    class="inline-flex items-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                Siguiente
            </button>
            <button type="submit" x-show="currentStep === totalSteps - 1"
                    class="inline-flex items-center rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-2.5 text-sm font-semibold text-white shadow-md hover:from-indigo-700 hover:to-purple-700">
                {{ $submitLabel }}
            </button>
        </div>
    </div>
</form>

@once
    @push('scripts')
        <script>
            function registerFormWizard() {
                Alpine.data('formWizard', (config = {}) => ({
                    currentStep: 0,
                    totalSteps: config.totalSteps ?? 1,
                    steps: config.steps ?? [],

                    progress() {
                        return this.totalSteps <= 1
                            ? 100
                            : ((this.currentStep + 1) / this.totalSteps) * 100;
                    },

                    panelFor(step) {
                        return this.$el.querySelector('[data-wizard-step="' + step + '"]');
                    },

                    validateStep() {
                        const panel = this.panelFor(this.currentStep);
                        if (!panel) {
                            return true;
                        }
                        const fields = panel.querySelectorAll('input, select, textarea');
                        for (const field of fields) {
                            if (field.disabled || field.type === 'hidden') {
                                continue;
                            }
                            if (field.offsetParent === null) {
                                continue;
                            }
                            if (!field.checkValidity()) {
                                field.reportValidity();
                                field.focus({ preventScroll: true });
                                return false;
                            }
                        }
                        return true;
                    },

                    notifyStepVisible() {
                        this.$nextTick(() => {
                            setTimeout(() => {
                                window.dispatchEvent(new CustomEvent('wizard-step-visible', {
                                    detail: { step: this.currentStep, form: this.$el },
                                }));
                            }, 120);
                        });
                    },

                    next() {
                        if (!this.validateStep()) {
                            return;
                        }
                        if (this.currentStep < this.totalSteps - 1) {
                            this.currentStep++;
                            this.notifyStepVisible();
                        }
                    },

                    prev() {
                        if (this.currentStep > 0) {
                            this.currentStep--;
                            this.notifyStepVisible();
                        }
                    },

                    goTo(i) {
                        if (i <= this.currentStep) {
                            this.currentStep = i;
                            this.notifyStepVisible();
                        }
                    },

                    onSubmit(event) {
                        if (this.currentStep < this.totalSteps - 1) {
                            event.preventDefault();
                            this.next();
                        }
                    },

                    init() {
                        for (let i = 0; i < this.totalSteps; i++) {
                            const panel = this.panelFor(i);
                            if (panel && panel.querySelector('.text-rose-600')) {
                                this.currentStep = i;
                                break;
                            }
                        }
                        this.notifyStepVisible();
                    },
                }));
            }

            document.addEventListener('alpine:init', registerFormWizard);
            if (window.Alpine) {
                registerFormWizard();
            }
        </script>
    @endpush
@endonce
