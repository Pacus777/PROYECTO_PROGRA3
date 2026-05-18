@props([
    'index' => 0,
    'title' => '',
    'description' => '',
])

<div
    data-wizard-step="{{ $index }}"
    x-show="currentStep === {{ (int) $index }}"
    x-cloak
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 translate-x-3"
    x-transition:enter-end="opacity-100 translate-x-0"
    x-effect="
        if (currentStep === {{ (int) $index }}) {
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('wizard-step-visible', {
                    detail: { step: {{ (int) $index }}, form: $el.closest('form') }
                }));
            }, 120);
        }
    "
    class="space-y-4"
    {{ $attributes }}
>
    @if($title)
        <div>
            <h3 class="text-base font-semibold text-slate-900">{{ $title }}</h3>
            @if($description)
                <p class="mt-0.5 text-sm text-slate-500">{{ $description }}</p>
            @endif
        </div>
    @endif
    {{ $slot }}
</div>
