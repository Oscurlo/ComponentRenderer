# Component Renderer

ComponentRenderer es una librería PHP inspirada en JSX que permite leer y procesar componentes mediante el uso de un búfer de salida. Esta herramienta facilita la construcción de interfaces dinámicas y modulares al permitir el uso de componentes de manera intuitiva.

## Instalación

Puedes instalar la librería a través de Composer:

```bash
composer require oscurlo/component-renderer
```

> Si no utilizas Composer, puedes descargar el archivo ZIP e incluir la ruta con los archivos manualmente.

```php
use Oscurlo\ComponentRenderer\ComponentRenderer;

include_once "/ruta/src/ComponentInterface.php";
include_once "/ruta/src/ComponentManager.php";
include_once "/ruta/src/ComponentExecutor.php";
include_once "/ruta/src/ComponentInterpreter.php";
include_once "/ruta/src/ComponentBuffer.php";
include_once "/ruta/src/ComponentRenderer.php";
```

## Uso

Para usar la librería, debes especificar las rutas de los componentes y el contenido a procesar.

> Intenté que sea lo más amigable posible para que lo adapten a sus necesidades.

```php
use Oscurlo\ComponentRenderer\ComponentRenderer;

include_once "vendor/autoload.php";

$components = [
    '/ruta/de/componentes' => 'MiComponente'
];

$componentRenderer = new ComponentRenderer($components);
# También puedes enviar los componentes con este método
# $componentRenderer->set_component_manager($components);
```

### Forma 1

```php
# Con el método render procesa e imprime el contenido
$componentRenderer->render(<<<XML
<MiComponente>
    ...
</MiComponente>
XML);
```

### Forma 2

```php
# Utilizando el método buffer para capturar el contenido
$componentRenderer->start();
echo <<<XML
<MiComponente>
    ...
</MiComponente>
XML;

# También puedes incluir una vista con el contenido
# include "vista.html";
$componentRenderer->end();
```

## Componentes

Para crear componentes puedes utilizar funciones o métodos estáticos. Consideraciones:

1. El nombre del componente debe coincidir con el nombre del archivo, con la primera letra en mayúscula.
2. El componente recibe un solo valor de tipo "object" con los atributos enviados al componente.
3. El valor de retorno debe ser un "string".
4. Si deseas agregar más componentes, puedes utilizar la clase Component. Esta clase se encarga de renderizar y retornar el resultado.
5. Si vas a usar clases para los componentes, evita el uso de "namespace".

> Aún estoy buscando una solución para este último punto. El problema radica en que incluyo el archivo del componente y ejecuto la función o método, pero el uso de "namespace" podría generar conflictos.

### valores por defecto

1. children: Contenido dentro de la etiqueta (también funciona como atributo).
2. textContent: Texto plano dentro de la etiqueta.

```JSON
{
    "children": "Elementos.",
    "textContent": "Texto.",
}
```

### Ejemplos

```php
use Oscurlo\ComponentRenderer\Component;

function MiComponente(object $props): string
{
    return Component::render(<<<HTML
    <p>{$props->children}</p>
    HTML, null);
}

```

```php

use Oscurlo\ComponentRenderer\Component;

class Layout
{
    public static function home(object $props): string
    {

        $props->title ??= "satoru gojo se murio :c";

        $components = [
            '/ruta/de/componentes' => ['Header', 'Footer']
        ];

        return Component::render(<<<XML
        <!DOCTYPE html>
        <html lang="es">
        
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>{$props->title}</title>
        </head>
        
        <body>
            <Header />
            {$props->children}
            <Footer />
        </body>
        
        </html>
        XML, $components);
    }
}

```

## Características

- Gestión de componentes: Permite definir y utilizar componentes personalizados en tus vistas.
- Buffering: Utiliza el búfer de salida para procesar el contenido HTML.
- Facilidad de uso: Inspirado en la sintaxis de JSX para una integración sencilla y familiar.

## Contribución

Si deseas contribuir a este proyecto, sigue estos pasos:

- Haz un fork del repositorio.
- Crea una nueva rama (git checkout -b nueva-rama).
- Realiza tus cambios y haz un commit (git commit -am 'Añadir nueva funcionalidad').
- Envía tus cambios al repositorio remoto (git push origin nueva-rama).
- Abre una Pull Request.

## Apóyame

Si te gusta mi trabajo y quieres apoyarme, considera invitarme un cafecito 😘.
[![Invítame un café](https://www.buymeacoffee.com/assets/img/custom_images/yellow_img.png)](https://www.buymeacoffee.com/oscurlo)
[![Donate with PayPal](https://raw.githubusercontent.com/stefan-niedermann/paypal-donate-button/master/paypal-donate-button.png)](<https://paypal.me/oscurlo?country.x=CO&locale.x=es_XC>)
