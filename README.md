# AutoGest — Grupo 5

Sistema web de gestión para un taller automotriz. Permite consultar servicios, solicitar citas y administrar clientes desde un panel privado. Proyecto académico de **Desarrollo de Aplicaciones Web — UAPA**, Actividad Práctica 4 / Fase 4, desarrollado sobre las Fases 1–3.

## Tecnologías y requisitos

- Laravel **13.35.0**, Blade y Eloquent; dependencias fijadas en `composer.lock`.
- PHP **8.4.1 o superior**, dentro de las versiones 8.4–8.5 admitidas por las dependencias fijadas. Laravel 13 tiene un mínimo base de 8.3, pero el `composer.lock` de esta entrega utiliza Symfony 8.1, que requiere 8.4.1. Entorno verificado: **PHP 8.5.11**.
- Composer 2; verificado con **2.10.3**.
- MySQL **8.4.11**, utilizado para la ejecución y las pruebas de integración. El esquema utiliza operaciones convencionales de MySQL/MariaDB; MariaDB no fue probado.
- Extensiones PHP: PDO, `pdo_mysql`, mbstring, openssl, tokenizer, xml, ctype, fileinfo, curl, dom y session. `pdo_sqlite` para la suite predeterminada.
- Git y un navegador moderno. Permisos de escritura en `storage/` y `bootstrap/cache/`.

CSS propio, tres imágenes locales y JavaScript mínimo. **No requiere Node/npm, compilación de assets ni paquetes de autenticación adicionales.** Node 26.10.0 y npm 11.19.1 estaban disponibles, pero no son requisitos.

## Obtener el proyecto

La entrega se prepara en `fase-4-despliegue`. Los commits son locales hasta que se realice la revisión humana; la rama puede no estar publicada aún en GitHub.

Cuando la rama esté publicada:

```bash
git clone https://github.com/100077890-hash/AutoGest-Grupo5.git
cd AutoGest-Grupo5
git switch fase-4-despliegue
```

Para reproducir la entrega antes de publicarla, clonar el repositorio local de Baldwin en otro directorio:

```bash
git clone --branch fase-4-despliegue "/Users/baldwinr/DESARROLLO/UAPA/Desarrollo de Aplicaciones Web/AutoGest Grupo 5" AutoGest-Grupo5
cd AutoGest-Grupo5
```

## Instalación desde cero

### 1. Dependencias y configuración

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Copiar `.env.example` **sólo en una instalación nueva**. En un proyecto ya configurado, conservar `.env` y su clave. `.env`, `vendor/`, logs y caches están ignorados por Git.

### 2. Base de datos y usuario dedicado

Iniciar MySQL. Conectarse con una cuenta que pueda crear bases y usuarios:

```bash
mysql -u root -p
```

En la instalación local de Homebrew con root sin contraseña, usar `mysql -u root` únicamente para este paso. No es la cuenta de la aplicación. No se requiere ejecutar `mysql_secure_installation` ni modificar la configuración global del servidor.

Ejecutar en MySQL, **sobre una instalación nueva**, sustituyendo el marcador por una contraseña local elegida por quien instala:

```sql
CREATE DATABASE autogest CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'autogest_app'@'localhost' IDENTIFIED BY 'REEMPLAZA_CON_CLAVE_LOCAL';
GRANT ALL PRIVILEGES ON autogest.* TO 'autogest_app'@'localhost';
```

Si la base o el usuario ya existen, comprobarlos y reutilizarlos; no borrarlos para instalar. La aplicación necesita permisos de DDL para sus migrations y de lectura/escritura sobre su propia base; no recibe permisos globales.

Editar `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=autogest
DB_USERNAME=autogest_app
DB_PASSWORD="LA_MISMA_CLAVE_LOCAL_ELEGIDA"
```

Los valores de contraseña mostrados son **marcadores**, no credenciales válidas. La contraseña MySQL es diferente de la contraseña de la cuenta demo de AutoGest. No compartir ni versionar `.env`.

### 3. Crear esquema y datos demo

```bash
php artisan config:clear
php artisan migrate --seed
php artisan serve --host=127.0.0.1 --port=8000
```

Abrir [http://127.0.0.1:8000](http://127.0.0.1:8000). Detener el servidor con `Ctrl+C`.

La instalación local ya preparada conserva una contraseña MySQL aleatoria en `.env`. Para probarla basta ejecutar `composer install`, `php artisan migrate --seed` y `php artisan serve`; no volver a crear el usuario ni sobrescribir `.env`.

### Alternativa SQL

`database/autogest.sql` contiene el mismo esquema generado por las migrations y los datos demo, incluidos los hashes seguros. Es una alternativa para una **base vacía**, después de configurar el usuario y `.env`:

```bash
mysql -h 127.0.0.1 -u autogest_app -p autogest < database/autogest.sql
php artisan migrate --seed
php artisan serve
```

No importar el SQL sobre una base que ya contiene tablas. El script no borra tablas; incluye `migrations`, de modo que Artisan reconoce el esquema importado y no intenta crearlo otra vez. Los seeders se pueden repetir sin duplicar los datos demo ni cambiar contraseñas existentes. Elegir migrations o SQL para la creación inicial, no ambos sobre la misma base con tablas.

Esta entrega prepara una instalación nueva. No importa automáticamente datos personales ni credenciales de una base de las fases anteriores. Para conservar datos de una instalación anterior, respaldarla y planificar su traslado antes de aplicar el esquema: se conservan las cinco entidades y sus claves, y se añade unicidad al correo de clientes.

## Usuario demo

- **Usuario:** `demo`
- **Contraseña:** `AutoGestDemo2026!`

Cuenta exclusivamente académica, creada por seeder o SQL. La base almacena un hash bcrypt, nunca la contraseña en texto plano. Incluye seis servicios, un cliente ficticio, un vehículo y una cita de ejemplo para el 15/12/2026; después de esa fecha seguirá registrada aunque no aparecerá entre las próximas citas.

## Funciones y recorrido de prueba

1. `/`: inicio con identidad y contenido de AutoGest.
2. `/servicios`: catálogo y precios obtenidos de la BD; filtro de búsqueda opcional con JavaScript.
3. `/citas`: registro de cliente, vehículo y cita, validado en el servidor y guardado mediante transacción. Reutiliza registros existentes y evita asociar la placa de otro cliente. El formulario público no modifica datos existentes: si no coinciden, el taller debe actualizarlos.
4. `/login`: acceso con usuario y contraseña; errores genéricos y límite de intentos.
5. `/admin`: resumen y hasta ocho próximas citas con sus relaciones.
6. `/admin/clientes`: listar, crear, editar y eliminar clientes. La confirmación funciona sin JavaScript. No se eliminan clientes con citas; los vehículos sin citas se eliminan junto al cliente, conforme al esquema original.
7. **Cerrar sesión**: POST con CSRF, invalidación de sesión y regeneración del token. Una ruta privada vuelve a exigir login.

Los formularios preservan datos válidos tras un error, muestran mensajes en español y usan CSRF. Blade escapa el contenido. Eloquent utiliza listas de asignación permitida; las rutas privadas tienen middleware `auth`. La sesión se regenera al iniciar, se almacena cifrada en archivos y las consultas usan parámetros. Fecha y hora de citas se validan en `America/Santo_Domingo`. La cita registra una solicitud; no se implementan disponibilidad, pagos ni notificaciones.

Los enlaces públicos anteriores `index.html`, `servicios.html` y `citas.html` redirigen a sus equivalentes Laravel. El frontend y backend antiguos se retiraron después de comprobar su sustitución; siguen disponibles en el historial Git.

## Organización y conceptos Laravel

- `routes/web.php`: rutas nombradas y recurso RESTful de clientes.
- `app/Http/Controllers/`: autenticación, clientes, citas y páginas/resumen.
- `app/Http/Requests/`: validación de clientes y citas.
- `app/Models/`: `Usuario`, `Cliente`, `Vehiculo`, `Servicio`, `Cita`, con claves originales y relaciones Eloquent.
- `resources/views/`: layout, vistas Blade y parcial de campos.
- `public/`: punto de entrada, CSS, JS e imágenes locales.
- `database/migrations/`, `database/seeders/`, `database/autogest.sql`: esquema y datos reproducibles.
- `tests/Feature/AutoGestTest.php`: pruebas funcionales esenciales.

**Inyección de dependencias:** `AppServiceProvider` enlaza `StatefulGuard` con el guard `web` de Laravel. El contenedor lo inyecta en el constructor de `AuthController`, que lo usa para iniciar y cerrar sesión. Es una dependencia real del framework, sin añadir capas artificiales.

## Verificaciones

```bash
composer validate --strict
composer audit
php artisan about
php artisan route:list --except-vendor
php artisan config:clear
php artisan view:clear
php artisan test
vendor/bin/pint --test
```

La suite predeterminada usa **SQLite en memoria**, sin tocar la base local de AutoGest. Cubre páginas públicas, rutas privadas, login válido/inválido y límite de intentos, regeneración de sesión, logout, CRUD/validación/integridad, citas/rollback/reutilización, escaping, seeders repetibles, entradas de tipo incorrecto y CSRF con middleware real. También fue ejecutada contra una base MySQL aislada durante la preparación de la entrega.

Se verificaron además la importación SQL, la equivalencia del esquema, una instalación desde un clon local sin `.env` ni `vendor/` y el recorrido en navegador a tamaño de escritorio y móvil. No ejecutar `migrate:fresh` sobre bases con datos que deban conservarse.

Para un servidor web distinto de Artisan, su raíz debe apuntar a **`public/`**. Mantener `APP_DEBUG=false`; con HTTPS, configurar `SESSION_SECURE_COOKIE=true`. El despliegue remoto queda fuera de esta entrega.

## Preparación de entrega

Rama `fase-4-despliegue`, destinada a una revisión posterior hacia `main`. La preparación no realiza push, abre PR ni crea `v4.0`; esas acciones quedan para la revisión humana. No se modificó el historial anterior ni la identidad Git.

## Integrantes

- Dilares Acosta Turbi
- Luis A. Frías M.
- Baldwin Rodriguez
- José G. Collado G.
- Adrian Fajardo F.
- José L. Rodríguez M.
- Natanael Taveras H.
- Carlos A. Veras C.
