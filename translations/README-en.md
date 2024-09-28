# Component Renderer

ComponentRenderer is a PHP library inspired by JSX that allows reading and processing components through output buffering. This tool makes it easier to build dynamic and modular interfaces by enabling the use of components in an intuitive way.

## Installation

You can install the library via Composer:

```bash
composer require oscurlo/component-renderer
```

> If you're not using Composer, you can download the ZIP file and manually include the path to the files.

```php
use Oscurlo\ComponentRenderer\ComponentRenderer;

include_once "/ruta/src/ComponentInterface.php";
include_once "/ruta/src/ComponentManager.php";
include_once "/ruta/src/ComponentExecutor.php";
include_once "/ruta/src/ComponentInterpreter.php";
include_once "/ruta/src/ComponentBuffer.php";
include_once "/ruta/src/ComponentRenderer.php";
```

## Usage

To use the library, you need to specify the paths to the components and the content to be processed.

> I tried to make it user-friendly so you can adapt it to your preferences.

```php
use Oscurlo\ComponentRenderer\ComponentRenderer;

include_once "vendor/autoload.php";

$components = [
    '/path/to/components' => 'MyComponent'
];

$componentRenderer = new ComponentRenderer($components);
# You can also pass the components using this method
# $componentRenderer->set_component_manager($components);
```

### Forma 1

```php
# The render method processes and outputs the content
$componentRenderer->render(<<<XML
<MyComponent>
    ...
</MyComponent>
XML);
```

### Forma 2

```php
# Use the buffer method to capture the content
$componentRenderer->start();
echo <<<XML
<MyComponent>
    ...
</MyComponent>
XML;

# You can also include a view with the content
# include "view.html";
$componentRenderer->end();
```

## Components

To create components, you can use functions or static methods. Keep in mind the following:

1. The component name must match the file name, with the first letter capitalized.
2. The component receives a single "object" parameter containing the attributes passed to the component.
3. The return value must be a "string".
4. If you want to add more components, you can use the Component class. This class handles rendering and returns the result.
5. If you're using classes for the components, avoid using namespaces.

> I'm still working on solving this last issue. The problem is that I include the component file and execute the function or method, but the use of namespaces might cause conflicts.

### Default Values

1. children: Content within the tag (also works as an attribute).
2. textContent: Plain text inside the tag.

```JSON
{
    "children": "Elements.",
    "textContent": "Text.",
}
```

### Examples

```php
use Oscurlo\ComponentRenderer\Component;

function MyComponent(object $props): string
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

        $props->title ??= "satoru gojo died :c";

        $components = [
            '/path/to/components' => ['Header', 'Footer']
        ];

        return Component::render(<<<XML
        <!DOCTYPE html>
        <html lang="en">
        
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

## Features

- Component management: Allows defining and using custom components in your views.
- Buffering: Uses output buffering to process HTML content.
- Ease of use: Inspired by JSX syntax for seamless and familiar integration.

## Contribution

If you want to contribute to this project, follow these steps:

- Fork the repository.
- Create a new branch (git checkout -b new-branch).
- Make your changes and commit them (git commit -am 'Add new functionality').
- Push your changes to the remote repository (git push origin new-branch).
- Open a Pull Request.

## Support Me

If you'd like to support my work, feel free to buy me a coffee 😘.

[![Invítame un café](https://www.buymeacoffee.com/assets/img/custom_images/yellow_img.png)](https://www.buymeacoffee.com/oscurlo)
[![Donate with PayPal](https://raw.githubusercontent.com/stefan-niedermann/paypal-donate-button/master/paypal-donate-button.png)](<https://paypal.me/oscurlo?country.x=CO&locale.x=es_XC>)
