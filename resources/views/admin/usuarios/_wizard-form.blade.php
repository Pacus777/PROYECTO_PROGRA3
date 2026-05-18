@props([
    'usuario' => null,
    'roles',
    'unidades',
    'action',
    'method' => 'POST',
    'submitLabel' => 'Guardar',
    'cancelUrl' => null,
    'modal' => null,
])

<x-ui.form-wizard
    :steps="['Datos personales', 'Cuenta y acceso', 'Confirmar']"
    :action="$action"
    :method="$method"
    :submit-label="$submitLabel"
    :cancel-url="$cancelUrl"
    :modal="$modal"
>
    <x-ui.form-wizard-step :index="0" title="Datos de la persona" description="Información del titular de la cuenta.">
        @include('admin.usuarios._form-persona', ['usuario' => $usuario])
    </x-ui.form-wizard-step>

    <x-ui.form-wizard-step :index="1" title="Cuenta de acceso" description="Rol, correo y contraseña.">
        @include('admin.usuarios._form-cuenta', ['usuario' => $usuario, 'roles' => $roles, 'unidades' => $unidades])
    </x-ui.form-wizard-step>

    <x-ui.form-wizard-step :index="2" title="Revisar y guardar" description="Verifica los datos antes de enviar.">
        <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 text-sm text-slate-600 space-y-2">
            <p>Al guardar se creará la cuenta con los datos ingresados en los pasos anteriores.</p>
            <p class="text-xs text-slate-500">Si necesitas corregir algo, usa <strong>Anterior</strong> o haz clic en el paso correspondiente arriba.</p>
        </div>
    </x-ui.form-wizard-step>
</x-ui.form-wizard>
