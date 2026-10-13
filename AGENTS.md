# AGENTS.md

Guía para agentes de IA que trabajen en **Tornado Framework 2.0** (Team Sferea BackEnd).

## Resumen del proyecto

Microframework PHP MVC construido sobre estas librerías (ver `composer.json`):

- **Zaphpa** (`zaphpa/zaphpa`): router y sistema de middlewares.
- **Mustache** (`mustache/mustache`): motor de plantillas.
- **Spot2** (`vlucas/spot2`): ORM / DataMapper.
- **Aura\Session** (`aura/session`): manejo de sesiones.
- **Monolog** (`monolog/monolog`): logging.
- **PHPMailer** (`phpmailer/phpmailer`): envío de correo.
- **OAuth2 Server** (`bshaffer/oauth2-server-php`): autenticación OAuth.
- **WideImage** (`smottt/wideimage`): manipulación de imágenes.
- **Whoops** (`filp/whoops`): manejo de errores en modo debug.

## Estructura de directorios

```
index.php              Punto de entrada: carga autoload, config y router
tornado/               Núcleo del framework
  router/init.php      Inicializa router, middlewares, controladores y modelos
  router/routes.php    Definición de rutas ($ROUTES)
  config_template.php  Plantilla de config (se copia a config.php, gitignored)
  mail_config_template.php  Plantilla de correo (se copia a mail_config.php, gitignored)
  Mustache.php         Middleware de plantillas Mustache
  SessionLogin.php     Middleware de autenticación / control de acceso
  Migrator.php         Middleware de migraciones y data fixtures
  AutoDocumentator.php Middleware de autodocumentación de API
  OAuth.php            Middleware OAuth
  UserCredentials.php  Implementación de credenciales OAuth
  CORS.php             Middleware CORS
controllers/           Controladores (autoload via classmap de Composer)
model/                 Entidades Spot en namespace `Entity` (autoload PSR-4)
views/                 Plantillas Mustache (subcarpetas por controlador)
views/partials/        Parciales Mustache reutilizables
utilerias/             Helpers (imágenes, videos, etc.)
resources/css/         CSS fuente de Tailwind (input)
assets/css/            CSS compilado (output, versionado)
tools/tailwindcss      CLI standalone de Tailwind (gitignored)
bin/build-css.sh       Descarga el CLI y compila el CSS
router.php             Router para `php -S` (sirve estáticos y delega a index.php)
package.json           Scripts npm equivalentes (si hay Node disponible)
vendor/                Dependencias Composer (gitignored)
migration/             Fixtures de datos generados por Migrator (si existe)
```

## Arquitectura y flujo

1. `index.php` carga `vendor/autoload.php`, `tornado/config.php` y `tornado/router/init.php`.
2. `init.php` registra los middlewares en orden: `Mustache`, `SessionLogin`, `Migrator`, `AutoDocumentator`, `OAuth`.
3. Las rutas se declaran en `tornado/router/routes.php` dentro del arreglo global `$ROUTES`.
4. Controladores, entidades y middlewares se cargan de forma perezosa mediante el autoload de Composer (ver abajo).
5. `.htaccess` redirige todo el tráfico a `index.php` (front controller).

## Autoloading (Composer)

Definido en `composer.json`, evita `require`/`scandir` manuales:

- PSR-4 `Tornado\` → `tornado/` y `utilerias/` (los helpers usan el namespace `Tornado`).
- PSR-4 `Entity\` → `model/`.
- `classmap` → `controllers/` (los controladores son clases globales; también mapea `Tornado\Controller`).

Los nombres de archivo deben coincidir con la clase (Linux es case-sensitive). Al mover/renombrar una clase, ejecutar `composer dump-autoload`.

## Convenciones de código

- **Idioma**: comentarios, nombres de variables y mensajes en **español**.
- **Indentación**: 4 espacios. Secciones del framework a veces con 2 espacios; respetar el archivo que se edita.
- **Namespace del framework**: `Tornado\` (middlewares y `Controller` base).
- **Namespace de entidades**: `Entity\` (clase en `model/` que extiende `\Spot\Entity`).
- **Controladores**: clase global (sin namespace) que extiende `Tornado\Controller`.
  - Métodos de acción reciben `($req, $res)`.
  - Render: `echo $this->renderView($res, $data);`
  - Parámetros de formulario: `$req->data["campo"]`; de URL: `$req->params["seccion"]`.
  - Plantilla Mustache: `$res->mustache->loadTemplate("Carpeta/archivo.mustache")`.
- **No agregar comentarios** salvo que se soliciten explícitamente; el código existente usa bloques de documentación en métodos.
- Sigue el estilo de los archivos vecinos antes de introducir nuevas librerías o patrones.

## Definición de rutas

En `tornado/router/routes.php`:

```php
$ROUTES=[
    "/home"=>[
        'path'=>'/',            // template/clave de ruta
        'allow'=>true,          // true = acceso sin login
        'rol'=>'admin',         // opcional: admin / agente / cliente
        'get'=>array('Plain','home')   // ['Controlador','metodo']
    ]
];
```

`allow` y `rol` son leídos por `SessionLogin::cargarPaths()` para control de acceso. Las rutas de `rol=admin` van a `$urlAdmin`; las de `allow=true` a `$urlPublicas`.

## Entidades (Spot ORM)

Basarse en `model/model.example`. Namespace `Entity`, extender `\Spot\Entity`, definir `$table`, `fields()` y opcionalmente `relations(Mapper, Entity)`. La entidad se resuelve por nombre de controlador (`Entity\<NombreControlador>`).

Anotar cada entidad con `@property` (una por campo y relación) para que el IDE autocomplete los campos; Spot2 no expone tipos propios.

## Autocompletado / IDE

`Tornado\Controller` tipa `$this->spot` y expone el helper genérico `mapper()`:

```php
$userMapper = $this->mapper(\Entity\User::class); // \Spot\Mapper<\Entity\User>
```

Usar siempre `::class` (no el string `'Entity\User'`) para que Intelephense infiera el tipo. Como los métodos de Spot devuelven `mixed`, anotar el resultado cuando se necesiten los campos:

```php
/** @var \Entity\User $user */
$user = $this->mapper(\Entity\User::class)->get($id);
```

## Configuración

`tornado/config.php` y `tornado/mail_config.php` están **gitignoreados**; se generan copiando las plantillas:

```bash
cp tornado/config_template.php tornado/config.php
cp tornado/mail_config_template.php tornado/mail_config.php
```

Editar ahí los datos de conexión MySQL (`dbname`, `user`, `password`, `host`) y correo. No commitear secretos.

## Comandos

```bash
composer install          # instalar dependencias
composer update           # actualizar dependencias
```

No hay framework de tests ni linter configurados. Para verificar cambios, ejecutar el servidor con el front controller y navegar las rutas:

```bash
php -S localhost:8000 router.php   # o apuntar un vhost al directorio y usar .htaccess
```

Rutas de utilidad expuestas por middlewares:

- `GET /apidocs` — documentación automática de endpoints.
- `GET /migrate/up` — migrar todas las entidades (crea tablas y carga fixtures).
- `GET /migrate/data` — exportar data fixtures a `migration/`.
- `GET /getData/{entity}` — exportar data de una entidad.

## Frontend (Tailwind CSS v4)

El proyecto usa Tailwind v4 con su **CLI standalone** (no requiere Node). El input es `resources/css/app.css` y el output `assets/css/app.css`, enlazado desde `views/partials/panel.mustache` en `/assets/css/app.css`.

El CSS se genera escaneando `views/` y `controllers/` (directivas `@source`). Al agregar clases nuevas en plantillas, recompilar:

```bash
bin/build-css.sh          # compila minificado (descarga el CLI si falta)
bin/build-css.sh watch    # recompila al guardar
```

Si hay Node disponible, equivalen a `npm run build:css` y `npm run watch:css`. El binario `tools/tailwindcss` está gitignored; `assets/css/app.css` sí se versiona para que la app funcione sin compilar.

## Reglas para agentes

- Ejecutar `composer install` si falta `vendor/` antes de probar.
- Nunca commitear `config.php`, `mail_config.php`, `vendor/`, `composer.lock` (ya gitignored) ni secretos.
- Mantener el idioma español y las convenciones de cada archivo.
- Al agregar un controlador, crear la entidad homónima en `model/` solo si realmente se requiere persistencia.
- Al agregar rutas, registrar la vista correspondiente en `views/<Controlador>/<metodo>.mustache` (o ajustar en el callback).
- No commitear cambios salvo que el usuario lo pida explícitamente.
