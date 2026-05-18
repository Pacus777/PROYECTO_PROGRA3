# Sistema de Admisión Escolar

Sistema web para la gestión de admisiones en unidades educativas. Permite administrar postulaciones, documentos, evaluaciones y resultados de estudiantes con control de acceso basado en roles.

**Stack:** Laravel 12 + React 19 + PostgreSQL + Tailwind CSS v4 + Vite

---

## Requisitos del sistema

Antes de comenzar, asegúrate de tener instalado:

| Herramienta | Versión mínima | Verificar con |
|---|---|---|
| PHP | 8.2 o superior | `php -v` |
| Composer | 2.x | `composer -V` |
| Node.js | 18.x o superior | `node -v` |
| npm | 9.x o superior | `npm -v` |
| PostgreSQL | 14 o superior | `psql --version` |
| Git | cualquier versión reciente | `git --version` |

> **Recomendado para Windows:** usar [Laragon](https://laragon.org/) ya que incluye PHP, Composer y herramientas de base de datos preconfiguradas.

---

## Instalación desde cero (primer uso)

### 1. Clonar el repositorio

```bash
git clone https://github.com/TU_USUARIO/sistema-admision-escolar.git
cd sistema-admision-escolar
```

### 2. Instalar dependencias de PHP

```bash
composer install
```

### 3. Instalar dependencias de Node.js

```bash
npm install
```

### 4. Configurar variables de entorno

```bash
cp .env.example .env
```

Luego abre el archivo `.env` y ajusta los valores de tu base de datos:

```env
APP_NAME=AdmisionEscolar

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=admision_escolar
DB_USERNAME=postgres
DB_PASSWORD=tu_password_aqui
```

### 5. Generar la clave de la aplicación

```bash
php artisan key:generate
```

### 6. Crear la base de datos en PostgreSQL

Abre tu cliente de PostgreSQL (psql, pgAdmin, TablePlus, etc.) y ejecuta:

```sql
CREATE DATABASE admision_escolar;
```

### 7. Ejecutar las migraciones y seeders

```bash
php artisan migrate --seed
```

Esto crea todas las tablas y carga los datos iniciales (roles, tipos de documento, unidades educativas y un usuario administrador).

### 8. Crear el enlace de almacenamiento

```bash
php artisan storage:link
```

### 9. Compilar los assets del frontend

**Para desarrollo (con hot reload):**
```bash
npm run dev
```

**Para producción:**
```bash
npm run build
```

### 10. Iniciar el servidor de desarrollo

Abre **dos terminales** en paralelo:

**Terminal 1 — Backend Laravel:**
```bash
php artisan serve
```

**Terminal 2 — Frontend Vite:**
```bash
npm run dev
```

La aplicación estará disponible en: [http://localhost:8000](http://localhost:8000)

---

## Credenciales por defecto

Tras ejecutar los seeders, puedes ingresar con:

| Rol | Email | Contraseña |
|---|---|---|
| Admin General | `admin@sistema.test` | `Admin123!` |

> Cambia estas credenciales antes de poner en producción.

---

## Roles del sistema

| Rol | Descripción |
|---|---|
| `admin_general` | Administrador del sistema, acceso total |
| `admin_institucional` | Administrador de una unidad educativa |
| `tutor` | Apoderado o tutor del estudiante |

Los **estudiantes postulantes** se registran como datos académicos (tabla `estudiante`), no como usuarios con login. El tutor los vincula por código y gestiona sus postulaciones.

---

## Comandos útiles de Laravel

```bash
# Ver todas las rutas disponibles
php artisan route:list

# Revertir y volver a ejecutar migraciones con seeders
php artisan migrate:fresh --seed

# Acceder a la consola interactiva
php artisan tinker

# Ejecutar tests
php artisan test

# Limpiar caché de la aplicación
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## Trabajar con el repositorio en equipo (flujo Git)

### Configuración inicial de Git (una sola vez por máquina)

```bash
git config --global user.name "Tu Nombre"
git config --global user.email "tu@email.com"
```

### Obtener los últimos cambios del repositorio remoto

Antes de comenzar a trabajar cada día, sincroniza con el equipo:

```bash
git pull origin main
```

### Flujo de trabajo recomendado

```bash
# 1. Crear una rama para tu tarea o funcionalidad
git checkout -b feature/nombre-de-tu-funcionalidad

# 2. Hacer tus cambios en el código...

# 3. Ver qué archivos cambiaste
git status

# 4. Agregar los archivos modificados
git add .

# 5. Confirmar los cambios con un mensaje descriptivo
git commit -m "feat: descripción clara de lo que hiciste"

# 6. Subir tu rama al repositorio remoto
git push origin feature/nombre-de-tu-funcionalidad

# 7. Desde GitHub, abrir un Pull Request hacia main
```

### Si alguien hizo cambios en main mientras trabajabas

```bash
# Actualiza tu rama con los cambios de main
git fetch origin
git merge origin/main

# O con rebase (historial más limpio)
git rebase origin/main
```

### Cuando tu PR se fusiona y quieres volver a main

```bash
git checkout main
git pull origin main

# Eliminar la rama local ya fusionada (opcional)
git branch -d feature/nombre-de-tu-funcionalidad
```

### Sincronizar base de datos cuando hay nuevas migraciones

Después de hacer `git pull`, si hay nuevos archivos en `database/migrations/`:

```bash
php artisan migrate
```

Si también hay nuevos seeders necesarios:

```bash
php artisan db:seed --class=NombreDelSeeder
```

---

## Estructura del proyecto

```
sistema-admision-escolar/
├── app/
│   ├── Http/
│   │   ├── Controllers/    # Controladores web y API
│   │   └── Middleware/     # Autenticación y roles
│   ├── Models/             # Modelos Eloquent
│   └── Services/           # Lógica de negocio
├── database/
│   ├── migrations/         # Migraciones de base de datos
│   └── seeders/            # Datos iniciales
├── resources/
│   ├── js/                 # React 19 + React Router + Axios
│   └── views/              # Blade templates
├── routes/
│   ├── web.php             # Rutas web
│   └── api.php             # Rutas API REST (v1)
├── public/                 # Entry point web
├── .env.example            # Plantilla de configuración
├── composer.json           # Dependencias PHP
├── package.json            # Dependencias Node.js
└── vite.config.js          # Configuración Vite
```

---

## Variables de entorno importantes

| Variable | Descripción | Valor por defecto |
|---|---|---|
| `APP_KEY` | Clave de cifrado (generar con `php artisan key:generate`) | — |
| `APP_ENV` | Entorno (`local`, `production`) | `local` |
| `APP_DEBUG` | Mostrar errores detallados | `true` |
| `DB_CONNECTION` | Motor de base de datos | `pgsql` |
| `DB_DATABASE` | Nombre de la base de datos | `admision_escolar` |
| `DB_USERNAME` | Usuario de PostgreSQL | `postgres` |
| `DB_PASSWORD` | Contraseña de PostgreSQL | *(vacío)* |
| `QUEUE_CONNECTION` | Driver de colas | `database` |
| `VITE_API_URL` | URL base de la API para el frontend | `/api/v1` |

---

## Problemas frecuentes

**Error: `.env` no encontrado**
> Ejecuta `cp .env.example .env` y luego `php artisan key:generate`.

**Error de conexión a PostgreSQL**
> Verifica que el servicio PostgreSQL esté corriendo y que las credenciales en `.env` sean correctas.

**La página carga pero los estilos no aparecen**
> Ejecuta `npm run build` o asegúrate de tener `npm run dev` corriendo en una terminal separada.

**Error 500 después de `git pull`**
> Probablemente hay nuevas migraciones. Ejecuta `php artisan migrate`.

**Error de permisos en `storage/` (Linux/Mac)**
> `chmod -R 775 storage bootstrap/cache`

---

## Licencia

MIT
