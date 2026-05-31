<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\Auth;

use App\Models\Persona;
use App\Models\UnidadEducativa;
use App\Support\EstudianteIdentificador;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreTutorRegistroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $rudes = $this->input('rudes', []);
        if (is_array($rudes)) {
            $this->merge([
                'rudes' => array_values(array_filter(array_map(
                    fn ($r) => EstudianteIdentificador::normalizarRude(is_string($r) ? $r : null),
                    $rudes,
                ))),
            ]);
        }

        $ci = $this->input('ci_per');
        $this->merge([
            'ci_per' => is_string($ci) && trim($ci) !== '' ? trim($ci) : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nombres_per' => ['required', 'string', 'max:120'],
            'ap_paterno_per' => ['required', 'string', 'max:80'],
            'ap_materno_per' => ['nullable', 'string', 'max:80'],
            'ci_per' => ['required', 'string', 'max:32', 'min:5'],
            'telefono_per' => ['nullable', 'string', 'max:40'],
            'correo_usu' => ['required', 'string', 'email', 'max:160', 'unique:usuario,correo_usu'],
            'password_usu' => ['required', 'string', 'min:8', 'confirmed'],
            'rudes' => ['required', 'array', 'min:1', 'max:8'],
            'rudes.*' => ['required', 'string', 'regex:'.EstudianteIdentificador::RUDE_REGEX],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $ci = $this->input('ci_per');
            if (is_string($ci) && $ci !== '') {
                $persona = Persona::query()
                    ->where('ci_per', $ci)
                    ->with(['usuario', 'estudiante'])
                    ->first();

                if ($persona !== null) {
                    if ($persona->usuario !== null) {
                        $v->errors()->add(
                            'ci_per',
                            'Este CI ya tiene una cuenta en el sistema. Inicie sesión con su correo registrado.',
                        );
                    } elseif ($persona->estudiante !== null) {
                        $v->errors()->add(
                            'ci_per',
                            'Este CI pertenece a un estudiante. Use el CI del tutor o apoderado, no el del postulante.',
                        );
                    }
                }
            }

            $rudes = $this->input('rudes', []);
            if (! is_array($rudes) || $rudes === []) {
                return;
            }

            $encontrados = 0;

            foreach ($rudes as $index => $rude) {
                $estudiante = EstudianteIdentificador::buscarPorCodigoOVinculo((string) $rude);
                if ($estudiante === null) {
                    $v->errors()->add(
                        "rudes.{$index}",
                        "No hay ningún estudiante registrado con el RUDE {$rude}. Solicite el alta en su unidad educativa.",
                    );
                } else {
                    $encontrados++;
                }
            }

            if ($encontrados === 0) {
                $v->errors()->add(
                    'rudes',
                    'Debe ingresar al menos un RUDE válido que ya exista en el sistema.',
                );
            }

            if (count($rudes) !== count(array_unique($rudes))) {
                $v->errors()->add('rudes', 'No repita el mismo RUDE más de una vez.');
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rudes.required' => 'Ingrese el RUDE de al menos un hijo o hija.',
            'rudes.*.regex' => 'Cada RUDE debe tener entre 8 y 12 dígitos numéricos.',
            'password_usu.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password_usu.confirmed' => 'Las contraseñas no coinciden.',
            'correo_usu.unique' => 'Este correo ya está registrado. Inicie sesión con su cuenta existente.',
            'ci_per.required' => 'La cédula de identidad es obligatoria.',
            'ci_per.min' => 'Ingrese un número de CI válido.',
        ];
    }

    protected function getRedirectUrl(): string
    {
        $codigo = session('postular_colegio');
        if (is_string($codigo) && $codigo !== '') {
            $unidad = UnidadEducativa::query()
                ->where('codigo_ued', $codigo)
                ->first();

            if ($unidad !== null) {
                return route('colegios.show', $unidad);
            }
        }

        return route('colegios.index');
    }
}
