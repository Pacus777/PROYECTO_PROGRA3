# Sistema de Admisión Escolar — Estado Actual

## Qué es y cómo está construido

El sistema es una plataforma web de gestión de admisiones escolares desarrollada con **Laravel 12 (PHP 8.2+)** en el backend, **PostgreSQL** como base de datos, y **React 19 + Vite + Tailwind CSS v4** para componentes de frontend embebidos en vistas Blade. La autenticación opera en dos capas: sesiones PHP personalizadas para el portal web (`web.auth` + `web.role`) y tokens Sanctum para la API REST bajo `/api/v1`. Los archivos subidos (documentos) se sirven desde el storage de Laravel con links simbólicos a `public/storage`. Las exportaciones se generan con OpenSpout (XLSX/CSV) y los PDFs con `barryvdh/laravel-dompdf`.

La arquitectura sigue un patrón MVC clásico con una capa de Services que encapsula la lógica de negocio. No hay repositorios intermedios: los controladores delegan directamente a servicios (ej. `PostulacionService`, `ResultadoInstitucionalService`), que a su vez usan modelos Eloquent. Las rutas están divididas en cuatro archivos separados que se incluyen desde `routes/web.php`:

```
routes/
├── web.php                  ← punto de entrada, incluye los siguientes
├── admin_general.php        ← rutas del ministerio (/admin/*)
├── admin_institucional.php  ← rutas del director/secretaría (/admin/institucional/*)
└── tutor_web.php            ← rutas del apoderado (/tutor/*)
```

### Estructura completa de directorios

```
sistema-admision-escolar/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/                              ← controladores API REST
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── UsuarioController.php
│   │   │   │   ├── EstudianteController.php
│   │   │   │   ├── PostulacionController.php
│   │   │   │   ├── TutorController.php
│   │   │   │   ├── UnidadEducativaController.php
│   │   │   │   └── GestionController.php
│   │   │   └── Web/
│   │   │       ├── Auth/
│   │   │       │   └── LoginController.php
│   │   │       ├── Admin/                        ← admin general (ministerio)
│   │   │       │   ├── UsuarioController.php
│   │   │       │   ├── GestionController.php
│   │   │       │   ├── UnidadEducativaController.php
│   │   │       │   ├── EstudianteController.php
│   │   │       │   ├── PostulacionNacionalController.php
│   │   │       │   ├── ReporteController.php
│   │   │       │   ├── TerritorioController.php
│   │   │       │   ├── TutorVinculoController.php
│   │   │       │   ├── EstudianteTutorVinculoController.php
│   │   │       │   ├── EstadoPostulacionController.php
│   │   │       │   └── TipoDocumentoController.php
│   │   │       ├── AdminInstitucional/           ← director/secretaría
│   │   │       │   ├── BaseInstitutionalController.php
│   │   │       │   ├── DashboardController.php
│   │   │       │   ├── PostulacionController.php
│   │   │       │   ├── AcademicController.php
│   │   │       │   ├── OfertaController.php
│   │   │       │   ├── CupoController.php
│   │   │       │   ├── EvaluacionController.php
│   │   │       │   ├── ResultadoController.php
│   │   │       │   ├── AsignacionController.php
│   │   │       │   ├── ListaEsperaController.php
│   │   │       │   ├── DocumentoController.php
│   │   │       │   ├── HistorialController.php
│   │   │       │   └── ReporteController.php
│   │   │       ├── Tutor/                        ← apoderado/familia
│   │   │       │   ├── TutorDashboardController.php
│   │   │       │   ├── TutorEstudianteController.php
│   │   │       │   ├── TutorPostulacionController.php
│   │   │       │   ├── TutorDocumentoController.php
│   │   │       │   ├── TutorResultadoController.php
│   │   │       │   ├── TutorSeguimientoController.php
│   │   │       │   ├── TutorPerfilController.php
│   │   │       │   └── Concerns/ResolvesTutorContext.php
│   │   │       ├── HomeController.php
│   │   │       ├── DashboardController.php       ← redirecciona según rol
│   │   │       └── TutorAssistantController.php  ← chat IA
│   │   ├── Middleware/
│   │   │   ├── EnsureWebAuthenticated.php
│   │   │   ├── EnsureWebUserHasRole.php
│   │   │   ├── EnsureUserHasRole.php             ← para API Sanctum
│   │   │   └── RedirectIfWebAuthenticated.php
│   │   └── Requests/                             ← validaciones por rol y acción
│   │       ├── Api/Auth/, Estudiante/, Postulacion/, Tutor/
│   │       └── Web/Admin/, AdminInstitucional/, Tutor/
│   ├── Models/                                   ← 34 modelos Eloquent
│   │   ├── Usuario.php, Rol.php, Persona.php
│   │   ├── Tutor.php, Estudiante.php
│   │   ├── UnidadEducativa.php, Gestion.php
│   │   ├── Nivel.php, Curso.php, Paralelo.php
│   │   ├── OfertaAcademica.php, Cupo.php
│   │   ├── Postulacion.php, EstadoPostulacion.php
│   │   ├── Documento.php, TipoDocumento.php, ProcesamientoOcr.php
│   │   ├── Criterio.php, TipoCriterio.php, Evaluacion.php
│   │   ├── Resultado.php, Asignacion.php, ListaEspera.php
│   │   ├── Historial.php, Notificacion.php
│   │   ├── Provincia.php, Municipio.php, Departamento.php, DistritoEducativo.php
│   │   └── DetalleBoletin.php, ResumenBoletin.php
│   ├── Services/                                 ← 20+ servicios de lógica de negocio
│   │   ├── AuthService.php, UsuarioService.php
│   │   ├── PostulacionService.php
│   │   ├── PostulacionInstitucionalService.php
│   │   ├── PostulacionNacionalService.php
│   │   ├── EstudianteService.php, DocumentoService.php
│   │   ├── OfertaInstitucionalService.php
│   │   ├── ResultadoInstitucionalService.php
│   │   ├── ListaEsperaInstitucionalService.php
│   │   ├── AdminDashboardChartService.php
│   │   ├── InstitucionalDashboardChartService.php
│   │   ├── AdminExportService.php
│   │   ├── InstitucionalExportService.php
│   │   ├── HistorialInstitucionalService.php
│   │   ├── TutorAssistantService.php             ← chat Claude AI
│   │   ├── AcademicInstitucionalService.php
│   │   └── TutorVinculoService.php
│   └── Support/
│       └── Roles.php                             ← constantes de roles
├── database/
│   ├── migrations/                               ← 26 migraciones
│   └── seeders/
├── resources/
│   ├── views/
│   │   ├── admin/
│   │   │   ├── usuarios/, gestiones/, unidades/, estudiantes/
│   │   │   ├── postulaciones/, tutores/, reportes/
│   │   │   ├── catalogos/, dashboard/
│   │   │   └── institucional/
│   │   │       ├── dashboard.blade.php
│   │   │       ├── academic/, ofertas/, postulaciones/
│   │   │       ├── evaluacion/, resultados/, asignacion/
│   │   │       ├── lista-espera/, documentos/, historial/, reportes/
│   │   └── tutor/
│   │       ├── postulaciones/, estudiantes/, documentos/
│   │       ├── resultados/, seguimiento/, perfil/
│   └── js/                                       ← React 19 (componentes embebidos)
├── routes/
│   ├── web.php, api.php
│   ├── admin_general.php, admin_institucional.php, tutor_web.php
│   └── console.php
└── .env, composer.json, package.json, vite.config.js
```

### Modelo de datos central

El núcleo del sistema gira en torno a cuatro entidades: `Postulacion`, `OfertaAcademica`, `Estudiante` y `Usuario`. Un `Usuario` con rol `tutor` está vinculado a una `Persona`, que a su vez puede ser `Tutor`. Un `Tutor` tiene muchos `Estudiante` a través de la tabla pivote `estudiante_tutor`. Un `Estudiante` puede postular a una `OfertaAcademica` (combinación de Gestión + Unidad + Nivel + Curso + Paralelo), generando una `Postulacion`. Sobre esa postulación se acumulan `Documento`, `Evaluacion` (por `Criterio`), y finalmente `Resultado` y `Asignacion`. Los estudiantes **no son usuarios del sistema**; se registran como datos académicos identificados por RUDE o CI.

---

## Qué está hecho

### Rol: Admin General (Ministerio de Educación)

Todo el CRUD de entidades maestras está implementado y funcional:

- **Usuarios:** crear, editar, activar/desactivar, asignar rol y unidad educativa.
- **Gestiones académicas:** crear y administrar años académicos con fechas y estado activo.
- **Unidades educativas:** CRUD completo con coordenadas geográficas y vinculación a territorio (provincia, municipio, distrito educativo).
- **Territorio:** navegación jerárquica de departamento → provincia → municipio → distrito → unidad educativa.
- **Estudiantes:** CRUD con vinculación a tutores, búsqueda por RUDE y CI.
- **Tutores:** listado nacional, ver estudiantes vinculados, vincular/desvincular manualmente.
- **Postulaciones nacionales:** vista filtreable por estado, gestión y departamento, con detalle de cada postulación.
- **Catálogos:** gestión de estados de postulación (Pendiente, Aprobado, Rechazado, etc.) y tipos de documento.
- **Reportes y exportaciones en Excel/CSV:** postulaciones, postulantes, resumen de unidades, unidades, tutores y usuarios.
- **Dashboard nacional** con KPIs agregados (gráficos de estado a nivel país).

### Rol: Admin Institucional (Director / Secretaría)

Es el rol más completo del sistema e implementa el flujo de admisión de extremo a extremo:

- **Estructura académica:** crear y gestionar Niveles (E.I., Primaria, Secundaria), Cursos (1ro, 2do…) y Paralelos (A, B, C) propios de la unidad.
- **Ofertas académicas:** combinar Gestión + Nivel + Curso + Paralelo en una oferta publicable, con asignación de cupos.
- **Gestión de postulaciones:** listado filtrable de todas las postulaciones de su unidad, cambio de estado individual.
- **Revisión de documentos:** ver documentos subidos por tutores, aprobar o rechazar cada uno, descargar archivos.
- **Evaluación con criterios:** definir criterios ponderados (por `TipoCriterio`), registrar puntaje por postulación y criterio. La restricción `unique(postulacion, criterio)` garantiza que cada criterio se evalúa una sola vez por postulante.
- **Generación de resultados:** botón "Sincronizar" que suma puntajes de evaluaciones, calcula el ranking y persiste en la tabla `resultado`.
- **Asignación automática de cupos:** ejecutar asignación que toma los top-N postulantes por oferta, crea registros en `asignacion` y envía al resto a `lista_espera` con número de orden.
- **Lista de espera:** ver y promover postulantes de la lista cuando se libera un cupo.
- **Historial/Auditoría:** registro de todos los cambios (quién, qué tabla, qué acción, datos antes/después).
- **Reportes institucionales exportables:** postulaciones, ofertas, resultados, asignaciones, lista de espera, historial, documentos, resumen de admisión.
- **Dashboard institucional** con gráficos del estado de postulaciones de su unidad.

### Rol: Tutor (Apoderado / Familia)

- **Perfil personal:** ver datos de la persona vinculada.
- **Registro de estudiantes:** buscar por RUDE o CI y vincular al perfil del tutor mediante la tabla pivote.
- **Crear postulaciones:** seleccionar un estudiante y una oferta académica disponible, generando la solicitud con estado inicial.
- **Subir documentos:** adjuntar archivos (Cédula, Acta de Nacimiento, etc.) a cada postulación.
- **Descargar y eliminar documentos propios.**
- **Ver estado de postulaciones:** seguimiento del estado actual.
- **Ver evaluaciones y resultados:** puntaje obtenido, ranking y si fue seleccionado o está en lista de espera.
- **Ver historial de movimientos** de sus postulaciones.
- **Chat con asistente IA** (Claude) vía `POST /asistente/chat`, con throttling de 30 req/min.

### Flujo de negocio completo implementado

El ciclo de vida de una admisión está completamente modelado:

```
Tutor registra estudiante (RUDE/CI)
  → Tutor crea postulación a oferta académica
  → Tutor sube documentos requeridos
  → Admin institucional revisa documentos y cambia estado
  → Admin institucional evalúa con criterios ponderados
  → Sistema calcula puntaje total
  → Admin sincroniza resultados → genera ranking
  → Admin ejecuta asignación → top-N obtienen cupo, resto a lista de espera
  → Tutor consulta resultado / posición en lista de espera
```

### Autenticación y seguridad

- Dos capas de autenticación independientes: sesiones web y tokens Sanctum para API.
- Middleware de rol aplicado en todas las rutas protegidas (`web.role`, `EnsureUserHasRole`).
- Aislamiento por unidad: el `Admin Institucional` solo accede a datos de su propia unidad (implementado en `BaseInstitutionalController`).
- Formularios con validación via `FormRequest` en cada acción.
- El rol `estudiante` fue eliminado en migración `2026_05_17_120000` (estudiantes no son usuarios del sistema).

---

## Qué falta por hacer

### Funcionalidades ausentes o incompletas

| Área | Estado |
|---|---|
| **Notificaciones por email** | La tabla `notificacion` existe pero el mail driver está en `log`; no hay envío real de correos al cambiar estado de postulación |
| **OCR de documentos** | `ProcesamientoOcr` está modelado en BD pero no hay integración con ningún servicio externo de OCR |
| **Notificaciones en tiempo real** | No hay WebSockets ni polling; los tutores deben recargar manualmente para ver cambios |
| **Fechas límite de postulación** | No hay campos de apertura/cierre de postulaciones en `OfertaAcademica`; cualquier oferta acepta postulaciones siempre |
| **Requisitos por oferta** | No hay campo para especificar qué documentos son obligatorios en cada oferta |
| **Límite de postulaciones por estudiante** | Un estudiante puede postular a múltiples ofertas sin restricción |
| **Sistema de apelaciones** | No hay flujo para que un tutor impugne una decisión |
| **Integración RUDE nacional** | La búsqueda de estudiantes por RUDE es local; no hay conexión con un registro nacional externo |
| **Pago de aranceles** | No hay modelo ni flujo de pago |
| **Gestión de calendario** | No hay control de período de inscripción, evaluación, publicación de resultados |
| **Dashboard público** | No existe una vista pública del proceso de admisión para quienes aún no están registrados |
| **Confirmación de cupo por tutor** | Después de la asignación, no hay paso de "aceptar cupo" por parte del tutor |
| **Impresión/PDF de comprobante** | No hay generación de comprobante de postulación o carta de admisión para el tutor (dompdf está instalado pero no usado en este flujo) |

### Deuda técnica identificada

- El chat con IA referencia `OPENAI_API_KEY` en `.env` pero el servicio es `TutorAssistantService` probablemente apuntando a Claude; las credenciales y el proveedor deben verificarse.
- Las tablas `detalle_boletin` y `resumen_boletin` (notas académicas) están migradas y modeladas pero no tienen controladores ni vistas; es funcionalidad incompleta.
- No hay seeders de datos de catálogo (estados de postulación, tipos de documento) documentados; el sistema puede arrancar vacío en esas tablas.
- La API REST (`/api/v1`) duplica parte de la lógica web pero no está documentada (sin Swagger/OpenAPI).

---

## Resumen: dónde está el sistema hoy

El sistema tiene **todo el flujo de admisión implementado de extremo a extremo**. Un tutor puede registrar a su hijo, postular a una oferta, subir documentos y ver el resultado. Un director puede revisar postulaciones, evaluarlas con criterios propios, generar rankings y asignar cupos automáticamente. El ministerio tiene visibilidad nacional y puede exportar datos.

Lo que **no está** es el siguiente nivel de madurez operativa: notificaciones automáticas, control de fechas del proceso, validación documental con OCR, comprobantes PDF para familias, y la conexión con registros externos como el RUDE nacional. El sistema está listo para piloto en una o pocas unidades educativas; para despliegue masivo necesita las notificaciones y el control de calendario como mínimo.
