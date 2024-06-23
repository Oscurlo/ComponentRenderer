# ComponentRenderer

![Packagist Version](https://img.shields.io/packagist/v/oscurlo/component-renderer)

ComponentRenderer es una librería PHP inspirada en JSX que permite leer y procesar componentes mediante el uso de un búfer de salida. Esta herramienta facilita la construcción de interfaces dinámicas y modulares al permitir el uso de componentes de una manera intuitiva.

## Instalación

Puedes instalar esta librería usando Composer. Ejecuta el siguiente comando:

bash
composer require oscurlo/component-renderer


## Uso

> El componente recibe un arreglo con todos los atributos enviados en la etiqueta por defecto recibe el children y el textContent

```JSON
{
    "children": "Elementos.",
    "textContent": "Texto.",
}
```

```php
function MiComponente(array $main = []): string
{
    $msg = $main["children"] ?? "Satoru Gojō no ha muerto";

    return <<<HTML
    <p>{$msg}</p>
    HTML;
}
```

```php
require 'vendor/autoload.php';

use Oscurlo\ComponentRenderer\ComponentRenderer;

$gestor = new ComponentRenderer('/ruta/de/componentes');

// Apertura de lectura del búfer
// start: recibe los componentes que debe procesar
$gestor->start(['MiComponente']);

print <<<HTML
<!-- Mensaje por defecto del componente -->
<MiComponente></MiComponente>
<!-- Mensaje personalizado -->
<MiComponente>Si está muerto y punto :(</MiComponente>
HTML;

// Cierre de lectura del búfer
$gestor->end();
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

Aunque mi proyecto pueda no tener mucha relevancia, es posible que a alguien le resulte útil y quiera mostrar su apoyo invitándome un café.
[![Invítame un café](https://www.buymeacoffee.com/assets/img/custom_images/yellow_img.png)](https://www.buymeacoffee.com/oscurlo)