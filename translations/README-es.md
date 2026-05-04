# Component Renderer

Component Renderer es una mini librería PHP para renderizar componentes HTML reutilizables, inspirada en JSX. Está construida sobre el `DOMDocument` nativo de PHP — sin paso de compilación, sin transpilador, sin framework pesado.

## Instalación

```bash
composer require oscurlo/component-renderer
```

## Requisitos

- PHP >= 8.0
- ext-dom
- ext-libxml

---

## Primeros pasos

```php
use Oscurlo\ComponentRenderer\ComponentRenderer;

require __DIR__ . '/vendor/autoload.php';

$renderer = new ComponentRenderer([
    'MiApp\\Componentes' => ['Tarjeta', 'Boton'],
]);

$renderer->render(<<<'HTML'
<Tarjeta titulo="Hola mundo">
    <Boton etiqueta="Clic aquí" />
</Tarjeta>
HTML);
```

---

## Registro de componentes

Pasa un mapa de componentes a `ComponentRenderer`, `Component::render()`, o `set_component_manager()`.

```php
$components = [
    // Directorio — resuelve MiComponente.php dentro de esa carpeta
    __DIR__ . '/components' => ['MiComponente'],

    // Namespace — resuelve namespace\NombreFuncion()
    'MiApp\\Componentes' => ['Header', 'Footer', 'Boton'],

    // Clase — resuelve MiClase::nombreMetodo()
    MiApp\UI\Bootstrap::class => ['card', 'accordion'],
];
```

Los tres tipos de registro soportados:

| Tipo | Clave | Valor |
|---|---|---|
| Directorio | Ruta absoluta a una carpeta | Nombre(s) del archivo de componente (sin `.php`) |
| Namespace | String del namespace PHP | Nombre(s) de función dentro de ese namespace |
| Clase | `NombreClase::class` | Nombre(s) de método estático |

---

## Tres modos de renderizado

### 1. Instancia — `ComponentRenderer::render()`

Ideal para renderizar páginas completas. Crea el renderer una vez y reutilízalo.

```php
use Oscurlo\ComponentRenderer\ComponentRenderer;

$renderer = new ComponentRenderer([
    'MiApp\\Componentes' => ['Layout', 'Tarjeta'],
]);

$renderer->render('<Layout><Tarjeta titulo="Hola" /></Layout>');
```

### 2. Buffer — `start()` / `end()`

Ideal cuando el HTML está distribuido en un archivo PHP con salida mezclada.

```php
$renderer->start();
?>
<Layout>
    <Tarjeta titulo="Hola">
        <p>Contenido aquí</p>
    </Tarjeta>
</Layout>
<?php
$renderer->end();
```

### 3. Estático — `Component::render()`

Ideal para renders puntuales o funciones helper. Devuelve el HTML como string en lugar de imprimirlo.

```php
use Oscurlo\ComponentRenderer\Component;

$html = Component::render(
    html: '<Tarjeta titulo="Hola"><p>Contenido</p></Tarjeta>',
    components: ['MiApp\\Componentes' => ['Tarjeta']],
);

echo $html;
```

---

## Plantillas — `Component::template()`

Renderiza un archivo PHP/HTML con una sintaxis `{{ }}` compilada al vuelo.

```php
use Oscurlo\ComponentRenderer\Component;

$html = Component::template(
    filename: __DIR__ . '/vistas/pagina.php',
    vars: ['titulo' => 'Hola', 'items' => $items],
    props: $props,  // opcional — disponible como $props dentro de la plantilla
);
```

### Sintaxis de plantillas

| Sintaxis | Se compila a | Para qué sirve |
|---|---|---|
| `{{ $variable }}` | `<?= $variable ?>` | Imprimir un valor |
| `{{ @codigo php }}` | `<?php codigo php ?>` | Ejecutar PHP (bucles, condiciones…) |

```html
<!-- vistas/pagina.php -->
<h1>{{ $titulo }}</h1>

<ul>
    {{ @foreach ($items as $item): }}
        <li>{{ $item['nombre'] }}</li>
    {{ @endforeach }}
</ul>

{{ @if ($props->mostrarPie): }}
    <footer>Listo</footer>
{{ @endif }}
```

---

## Escribir componentes

Un componente es una **función** o un **método estático de clase** que recibe un objeto `$props` y devuelve un string HTML.

### Componente función

```php
namespace MiApp\Componentes;

use Oscurlo\ComponentRenderer\ComponentManager;

function Tarjeta(object $props): string
{
    $props->titulo ??= '';
    $attrs = ComponentManager::get_attributes($props, exclude: ['titulo']);

    return <<<HTML
    <div class="card" {$attrs}>
        <div class="card-header">{$props->titulo}</div>
        <div class="card-body">{$props->children}</div>
    </div>
    HTML;
}
```

### Componente clase (método estático)

```php
namespace MiApp\UI;

use Oscurlo\ComponentRenderer\Component;

class Layout
{
    public static function pagina(object $props): string
    {
        $props->titulo ??= 'Sin título';

        return Component::template(__DIR__ . '/layout.php', props: $props);
    }
}
```

Se registra como:

```php
MiApp\UI\Layout::class => ['pagina']
```

Se usa como:

```html
<Layout::pagina titulo="Inicio">
    <p>Contenido de la página</p>
</Layout::pagina>
```

---

## Props

Cada atributo en la etiqueta del componente se convierte en una propiedad del objeto `$props`.

| Prop | Descripción |
|---|---|
| `$props->children` | HTML interno de la etiqueta |
| `$props->textContent` | Texto plano del contenido (sin etiquetas hijas) |
| `$props->cualquierAtributo` | Cualquier otro atributo pasado en la etiqueta |

```html
<Boton type="submit" disabled="true">Guardar</Boton>
```

```php
function Boton(object $props): string
{
    // $props->type        → "submit"  (string)
    // $props->disabled    → "true"    (string — usa PropsCaster para castear)
    // $props->children    → "Guardar"
    // $props->textContent → "Guardar"
}
```

---

## PropsCaster — props con tipo

Los atributos HTML siempre llegan como strings. Usa `PropsCaster::interface()` para castearlos al tipo PHP correcto.

```php
use Oscurlo\ComponentRenderer\PropsCaster;

function BarraProgreso(object $props): string
{
    PropsCaster::interface([
        'valor'   => 'int',
        'maximo'  => 'int',
        'rayado'  => 'bool',
        'animar'  => 'bool',
    ], $props);

    // $props->valor  → 75    (int)
    // $props->rayado → true  (bool)
    // ...
}
```

Tipos soportados:

| String de tipo | Cast PHP |
|---|---|
| `bool` / `boolean` | `filter_var()` — soporta `"true"`, `"false"`, `"1"`, `"0"` |
| `int` / `integer` | `intval()` |
| `float` | `floatval()` |
| `double` | `doubleval()` |
| `string` / `str` | `(string)` |
| `array` | `json_decode($value, true)` |
| `object` | `json_decode($value)` |

---

## Métodos helper — `ComponentManager`

| Método | Descripción |
|---|---|
| `ComponentManager::get_attributes($props, $exclude)` | Construye un string de atributos HTML desde props, omitiendo `children`, `textContent` y cualquier clave extra que indiques |
| `ComponentManager::extract_attributes($props, $columns)` | Construye un string de atributos solo con las columnas indicadas |
| `ComponentManager::print(...$strings)` | Hace echo de strings separados por espacio |

```php
function Input(object $props): string
{
    $props->id ??= uniqid();
    $props->{'label-text'} ??= '';

    // Construye: id="..." type="..." class="..." (omite label-text, children, textContent)
    $attrs = ComponentManager::get_attributes($props, exclude: ['label-text']);

    return <<<HTML
    <label for="{$props->id}">{$props->{'label-text'}}</label>
    <input {$attrs}>
    HTML;
}
```

---

## `HtmlHelper` — utilidades HTML internas

Disponible en `Oscurlo\ComponentRenderer\Support\HtmlHelper`.

| Método | Descripción |
|---|---|
| `HtmlHelper::wrap($html, $encoding)` | Envuelve un fragmento en un documento HTML mínimo para que DOMDocument pueda parsearlo |
| `HtmlHelper::unwrap($html)` | Extrae el contenido del `<body>` de un documento HTML completo |
| `HtmlHelper::isDocument($html)` | Retorna `true` si el string contiene un documento `<html>...</html>` completo |

---

## Ejemplos

| Archivo | Qué muestra |
|---|---|
| [example1.php](../examples/example1.php) | Múltiples componentes — funciones, namespaces, métodos de clase |
| [example2.php](../examples/example2.php) | Buffer de salida con `start()` / `end()` |
| [example3.php](../examples/example3.php) | Plantillas con variables vía `Component::template()` |
| [example4.php](../examples/example4.php) | API estática con `Component::render()` |
| [example5.php](../examples/example5.php) | `PropsCaster` — props con tipo (bool, int, array…) |
| [example6.php](../examples/example6.php) | Componentes anidados + `Bootstrap::accordion` dentro de una card |

---

## Estructura del proyecto

```
src/
├── Contracts/
│   └── RendererInterface.php    # Contrato público para renderers
├── Concerns/
│   ├── HandlesDom.php           # Estado DOM + get_params()
│   ├── HandlesAttributes.php    # get_attributes(), extract_attributes()
│   └── RendersOutput.php        # print(), start(), end()
├── Support/
│   └── HtmlHelper.php           # wrap(), unwrap(), isDocument()
├── PropsCaster.php              # Type casting de props
├── ComponentInterface.php       # Alias @deprecated de PropsCaster
├── ComponentRegistry.php        # Registro y resolución de componentes
├── ComponentManager.php         # Fachada — conecta todos los concerns
├── ComponentExecutor.php        # Ejecuta un componente individual
├── ComponentInterpreter.php     # Parsea el HTML y dirige la ejecución
├── ComponentBuffer.php          # Buffering con start() / end()
├── ComponentRenderer.php        # Punto de entrada principal (API instancia)
└── Component.php                # Punto de entrada estático
```

---

## Contribución

1. Haz fork del repositorio.
2. Crea una rama nueva: `git checkout -b feature/mi-feature`
3. Realiza tus cambios y agrega tests.
4. Corre la suite de tests: `composer test`
5. Abre un Pull Request.

---

## Apóyame

Si la librería te es útil, puedes apoyarme:

[![Invítame un café](https://www.buymeacoffee.com/assets/img/custom_images/yellow_img.png)](https://www.buymeacoffee.com/oscurlo)
[![Donate with PayPal](https://raw.githubusercontent.com/stefan-niedermann/paypal-donate-button/master/paypal-donate-button.png)](https://paypal.me/oscurlo?country.x=CO&locale.x=es_XC)
