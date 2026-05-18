<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TutorAssistantService
{
    /**
     * @return array{message: string, links: list<array{label: string, url: string}>, suggestions: list<string>}
     */
    public function reply(string $userMessage, string $context): array
    {
        $message = trim($userMessage);
        if ($message === '') {
            return $this->welcome($context);
        }

        if ($this->useOpenAi()) {
            $ai = $this->replyWithOpenAi($message, $context);
            if ($ai !== null) {
                return $ai;
            }
        }

        return $this->replyWithRules($message, $context);
    }

    /**
     * @return array{message: string, links: list<array{label: string, url: string}>, suggestions: list<string>}
     */
    public function welcome(string $context): array
    {
        if ($context === 'tutor') {
            return [
                'message' => '¡Hola! Soy tu asistente de AdmisiónEscolar. Puedo orientarte sobre estudiantes, postulaciones, documentos, seguimiento y resultados. ¿En qué te ayudo?',
                'links' => [
                    ['label' => 'Nueva postulación', 'url' => route('tutor.postulaciones.create')],
                ],
                'suggestions' => [
                    '¿Cómo registro una postulación?',
                    '¿Dónde subo documentos?',
                    'Ver seguimiento de mi postulación',
                ],
            ];
        }

        return [
            'message' => '¡Hola! Soy el asistente de AdmisiónEscolar. Te explico cómo funciona la admisión, cómo ingresar al sistema y qué necesitas como familia o tutor.',
            'links' => [
                ['label' => 'Iniciar sesión', 'url' => route('login.show')],
            ],
            'suggestions' => [
                '¿Cómo funciona el proceso?',
                '¿Cómo inicio sesión?',
                '¿Quién puede postular?',
            ],
        ];
    }

    private function useOpenAi(): bool
    {
        $key = config('services.openai.key');

        return is_string($key) && strlen(trim($key)) > 10;
    }

    /**
     * @return array{message: string, links: list<array{label: string, url: string}>, suggestions: list<string>}|null
     */
    private function replyWithOpenAi(string $message, string $context): ?array
    {
        try {
            $response = Http::timeout(15)
                ->withToken((string) config('services.openai.key'))
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('services.openai.model', 'gpt-4o-mini'),
                    'temperature' => 0.4,
                    'max_tokens' => 400,
                    'messages' => [
                        ['role' => 'system', 'content' => $this->systemPrompt($context)],
                        ['role' => 'user', 'content' => $message],
                    ],
                ]);

            if (! $response->successful()) {
                return null;
            }

            $content = $response->json('choices.0.message.content');
            if (! is_string($content) || trim($content) === '') {
                return null;
            }

            $parsed = $this->replyWithRules($message, $context);
            $links = $parsed['links'];

            return [
                'message' => trim($content),
                'links' => $links,
                'suggestions' => $parsed['suggestions'],
            ];
        } catch (\Throwable) {
            return null;
        }
    }

    private function systemPrompt(string $context): string
    {
        $base = 'Eres un asistente amable de AdmisiónEscolar (Bolivia). Responde en español, breve (máximo 3 párrafos cortos). '
            .'No inventes trámites oficiales del Ministerio. Si no sabes algo, indica contactar al administrador del colegio. ';

        if ($context === 'tutor') {
            return $base.'El usuario es un TUTOR ya logueado. Rutas útiles: '
                .'estudiantes='.route('tutor.estudiantes.index').', '
                .'nueva postulación='.route('tutor.postulaciones.create').', '
                .'postulaciones='.route('tutor.postulaciones.index').', '
                .'documentos='.route('tutor.documentos.index').', '
                .'seguimiento='.route('tutor.seguimiento.index').', '
                .'resultados='.route('tutor.resultados.index').', '
                .'perfil='.route('tutor.perfil.index').'. '
                .'Menciona enlaces en texto plano si ayuda.';
        }

        return $base.'El usuario visita la página pública. Invítalo a iniciar sesión en '.route('login.show')
            .' si quiere postular. Secciones: #inicio, #como-funciona, #beneficios, #contacto.';
    }

    /**
     * @return array{message: string, links: list<array{label: string, url: string}>, suggestions: list<string>}
     */
    private function replyWithRules(string $message, string $context): array
    {
        $text = $this->normalize($message);

        if ($this->matches($text, ['hola', 'buenas', 'ayuda', 'hi', 'hello'])) {
            return $this->welcome($context);
        }

        $intents = $context === 'tutor' ? $this->tutorIntents() : $this->landingIntents();

        foreach ($intents as $intent) {
            foreach ($intent['keys'] as $key) {
                if (str_contains($text, $this->normalize($key))) {
                    return [
                        'message' => $intent['message'],
                        'links' => $intent['links'] ?? [],
                        'suggestions' => $intent['suggestions'] ?? [],
                    ];
                }
            }
        }

        return [
            'message' => $context === 'tutor'
                ? 'No encontré una respuesta exacta. Prueba preguntar por «postulación», «documentos», «estudiantes» o «seguimiento». También puedes usar el menú lateral.'
                : 'No estoy seguro de entender. Puedes preguntar cómo funciona el proceso, cómo iniciar sesión o qué es AdmisiónEscolar.',
            'links' => $context === 'tutor'
                ? [['label' => 'Ir al inicio del tutor', 'url' => route('tutor.dashboard')]]
                : [['label' => 'Iniciar sesión', 'url' => route('login.show')]],
            'suggestions' => $context === 'tutor'
                ? ['¿Cómo hago una postulación?', '¿Dónde veo mis documentos?']
                : ['¿Cómo funciona?', '¿Cómo inicio sesión?'],
        ];
    }

    /**
     * @return list<array{keys: list<string>, message: string, links?: list<array{label: string, url: string}>, suggestions?: list<string>}>
     */
    private function landingIntents(): array
    {
        return [
            [
                'keys' => ['como funciona', 'proceso', 'pasos', 'funciona'],
                'message' => 'El flujo es: el tutor inicia sesión, vincula al estudiante (RUDE), crea la postulación a una unidad educativa, sube los documentos y hace seguimiento hasta el resultado.',
                'links' => [
                    ['label' => 'Ver cómo funciona', 'url' => route('home').'#como-funciona'],
                    ['label' => 'Iniciar sesión', 'url' => route('login.show')],
                ],
                'suggestions' => ['¿Quién puede postular?', '¿Cómo inicio sesión?'],
            ],
            [
                'keys' => ['iniciar sesion', 'login', 'entrar', 'acceso', 'cuenta'],
                'message' => 'Las familias y tutores ingresan con el correo y contraseña que les asignó el colegio o el administrador del sistema.',
                'links' => [
                    ['label' => 'Ir a iniciar sesión', 'url' => route('login.show')],
                ],
                'suggestions' => ['¿Cómo funciona el proceso?', '¿Qué es AdmisiónEscolar?'],
            ],
            [
                'keys' => ['postular', 'postulacion', 'inscri', 'matricula', 'admision'],
                'message' => 'La postulación se realiza dentro del panel del tutor, después de iniciar sesión. Necesitas tener al estudiante vinculado a tu cuenta.',
                'links' => [
                    ['label' => 'Iniciar sesión como tutor', 'url' => route('login.show')],
                ],
                'suggestions' => ['¿Qué documentos necesito?', '¿Cómo funciona?'],
            ],
            [
                'keys' => ['documento', 'papel', 'requisito', 'certificado'],
                'message' => 'Los documentos dependen del tipo de postulación y del colegio. Una vez dentro del sistema, el tutor los sube en la sección de documentos de cada postulación.',
                'links' => [
                    ['label' => 'Iniciar sesión', 'url' => route('login.show')],
                ],
                'suggestions' => ['¿Cómo postulo?', '¿Cómo funciona?'],
            ],
            [
                'keys' => ['beneficio', 'ventaja', 'por que'],
                'message' => 'AdmisiónEscolar centraliza postulaciones, documentos y seguimiento en un solo lugar, con transparencia para familias e instituciones.',
                'links' => [
                    ['label' => 'Ver beneficios', 'url' => route('home').'#beneficios'],
                ],
                'suggestions' => ['¿Cómo funciona?', 'Iniciar sesión'],
            ],
            [
                'keys' => ['contacto', 'soporte', 'telefono', 'correo', 'ayuda humana'],
                'message' => 'Para trámites específicos de un colegio, contacta a la secretaría o dirección de la unidad educativa. Para acceso al sistema, solicita credenciales al administrador.',
                'links' => [
                    ['label' => 'Sección de contacto', 'url' => route('home').'#contacto'],
                ],
                'suggestions' => ['¿Cómo inicio sesión?'],
            ],
            [
                'keys' => ['tutor', 'apoderado', 'padre', 'madre', 'familia'],
                'message' => 'El rol de tutor (o apoderado) gestiona las postulaciones de uno o más estudiantes: vinculación, formularios, documentos y consulta de resultados.',
                'links' => [
                    ['label' => 'Acceso tutores', 'url' => route('login.show')],
                ],
                'suggestions' => ['¿Cómo postulo?', '¿Cómo funciona?'],
            ],
            [
                'keys' => ['que es', 'admisionescolar', 'sistema', 'plataforma'],
                'message' => 'AdmisiónEscolar es la plataforma digital para gestionar admisiones escolares: postulantes, unidades educativas, documentación y resultados.',
                'links' => [
                    ['label' => 'Conocer la plataforma', 'url' => route('home').'#inicio'],
                ],
                'suggestions' => ['¿Cómo funciona?', 'Iniciar sesión'],
            ],
        ];
    }

    /**
     * @return list<array{keys: list<string>, message: string, links?: list<array{label: string, url: string}>, suggestions?: list<string>}>
     */
    private function tutorIntents(): array
    {
        return [
            [
                'keys' => ['estudiante', 'vincul', 'rude', 'hijo', 'hija'],
                'message' => 'En «Estudiantes» ves y gestionas los postulantes vinculados a tu cuenta. Si no aparece ninguno, un administrador debe asignarte al estudiante antes de postular.',
                'links' => [
                    ['label' => 'Mis estudiantes', 'url' => route('tutor.estudiantes.index')],
                ],
                'suggestions' => ['¿Cómo hago una postulación?', 'No veo estudiantes'],
            ],
            [
                'keys' => ['no veo estudiante', 'sin estudiante', 'no tengo estudiante', 'vacio'],
                'message' => 'Si no tienes estudiantes vinculados, contacta al administrador de tu unidad educativa o al Ministerio (según tu caso). Ellos deben asociar tu usuario con el registro del estudiante.',
                'links' => [
                    ['label' => 'Ir a estudiantes', 'url' => route('tutor.estudiantes.index')],
                ],
                'suggestions' => ['¿Cómo postulo?', 'Ver mis postulaciones'],
            ],
            [
                'keys' => ['nueva postulacion', 'crear postulacion', 'registrar postulacion', 'postular'],
                'message' => 'Para crear una postulación: menú «Nueva postulación» o el botón en tu inicio. Elige gestión, unidad educativa y estudiante vinculado.',
                'links' => [
                    ['label' => 'Nueva postulación', 'url' => route('tutor.postulaciones.create')],
                ],
                'suggestions' => ['¿Dónde subo documentos?', 'Ver postulaciones'],
            ],
            [
                'keys' => ['postulacion', 'mis postulacion', 'lista postulacion'],
                'message' => 'En «Postulaciones» consultas todas tus solicitudes, su estado y el detalle de cada una.',
                'links' => [
                    ['label' => 'Mis postulaciones', 'url' => route('tutor.postulaciones.index')],
                ],
                'suggestions' => ['Nueva postulación', 'Seguimiento'],
            ],
            [
                'keys' => ['documento', 'subir', 'archivo', 'pdf', 'foto'],
                'message' => 'Los documentos se suben por postulación. Entra a la postulación y usa la opción de adjuntar archivos, o ve al listado general en «Documentos».',
                'links' => [
                    ['label' => 'Mis documentos', 'url' => route('tutor.documentos.index')],
                    ['label' => 'Nueva postulación', 'url' => route('tutor.postulaciones.create')],
                ],
                'suggestions' => ['¿Cómo va mi postulación?', 'Ver postulaciones'],
            ],
            [
                'keys' => ['seguimiento', 'estado', 'avance', 'progreso', 'revisar'],
                'message' => '«Seguimiento» muestra el estado actual de tus postulaciones: revisión, observaciones y cambios de etapa.',
                'links' => [
                    ['label' => 'Seguimiento', 'url' => route('tutor.seguimiento.index')],
                ],
                'suggestions' => ['Ver resultados', 'Mis postulaciones'],
            ],
            [
                'keys' => ['resultado', 'aceptado', 'rechazado', 'lista espera', 'cupo'],
                'message' => 'Cuando el colegio publique resultados, podrás consultarlos en la sección «Resultados».',
                'links' => [
                    ['label' => 'Resultados', 'url' => route('tutor.resultados.index')],
                ],
                'suggestions' => ['Seguimiento', 'Mis postulaciones'],
            ],
            [
                'keys' => ['perfil', 'cuenta', 'correo', 'contrasena', 'password', 'datos personales'],
                'message' => 'En «Mi perfil» revisas los datos de tu cuenta de tutor. Si necesitas cambiar credenciales, solicítalo al administrador del sistema.',
                'links' => [
                    ['label' => 'Mi perfil', 'url' => route('tutor.perfil.index')],
                ],
                'suggestions' => ['Ir al inicio', 'Mis postulaciones'],
            ],
            [
                'keys' => ['inicio', 'panel', 'dashboard', 'resumen'],
                'message' => 'Tu inicio muestra un resumen: estudiantes vinculados, postulaciones recientes y accesos rápidos.',
                'links' => [
                    ['label' => 'Inicio del tutor', 'url' => route('tutor.dashboard')],
                ],
                'suggestions' => ['Nueva postulación', 'Seguimiento'],
            ],
            [
                'keys' => ['cerrar sesion', 'salir', 'logout'],
                'message' => 'Para cerrar sesión usa el menú de tu usuario (arriba a la derecha) y elige «Cerrar sesión».',
                'links' => [],
                'suggestions' => ['Ir al inicio'],
            ],
        ];
    }

  /**
     * @param  list<string>  $keywords
     */
    private function matches(string $text, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($text, $this->normalize($keyword))) {
                return true;
            }
        }

        return false;
    }

    private function normalize(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ'],
            ['a', 'e', 'i', 'o', 'u', 'u', 'n'],
            $text
        );

        return preg_replace('/\s+/', ' ', $text) ?? $text;
    }
}
