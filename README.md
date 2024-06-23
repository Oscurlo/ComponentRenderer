# ComponentRenderer

![Packagist Version](https://img.shields.io/packagist/v/oscurlo/component-renderer)

ComponentRenderer is a PHP library inspired by JSX that allows you to read and process components using output buffering. This tool facilitates the construction of dynamic and modular interfaces by allowing the use of components in an intuitive way.

## Installation

You can install this library using Composer. Run the following command:

```bash
composer require oscurlo/component-renderer
```

## Uso

>The component receives an array with all the attributes sent in the tag. By default, it receives 'children' and 'textContent'.

```JSON
{
    "children": "Elements.",
    "textContent": "Text.",
}
```

```php
function MyComponent(array $main = []): string
{
    $msg = $main["children"] ?? "Satoru Gojō is not dead";

    return <<<HTML
    <p>{$msg}</p>
    HTML;
}
```

```php
require 'vendor/autoload.php';

use Oscurlo\ComponentRenderer\ComponentRenderer;

$renderer = new ComponentRenderer('/path/to/components');

// Start output buffering
// start: receives the components that it should process
$renderer->start(['MyComponent']);

print <<<HTML
<!-- Default message from the component -->
<MyComponent></MyComponent>
<!-- Custom message -->
<MyComponent>He is dead, period :(</MyComponent>
HTML;

// End output buffering
$renderer->end();
```

## Features

- Component management: Allows you to define and use custom components in your views.
- Buffering: Uses output buffering to process HTML content.
- Ease of use: Inspired by JSX syntax for simple and familiar integration.

## Contribution

If you want to contribute to this project, follow these steps:

- Fork the repository.
- Create a new branch (git checkout -b new-branch).
- Make your changes and commit them (git commit -am 'Add new feature').
- Push your changes to the remote repository (git push origin new-branch).
- Open a Pull Request.

## Support Me

Although my project may not have much relevance, it might be useful to someone who wants to show their support by buying me a coffee.
[![Invítame un café](https://www.buymeacoffee.com/assets/img/custom_images/yellow_img.png)](https://www.buymeacoffee.com/oscurlo)