# Component Renderer

![Packagist Version](https://img.shields.io/packagist/v/oscurlo/component-renderer)
[![Total Downloads](https://poser.pugx.org/oscurlo/component-renderer/downloads)](https://packagist.org/packages/oscurlo/component-renderer)
![PHP](https://img.shields.io/packagist/php-v/oscurlo/component-renderer)
![License](https://img.shields.io/packagist/l/oscurlo/component-renderer)

A lightweight PHP library for rendering reusable HTML components — inspired by JSX, built on DOMDocument, no build step required.

## Documentation

- [English](./translations/README-en.md)
- [Español](./translations/README-es.md)

## Quick start

```bash
composer require oscurlo/component-renderer
```

```php
use Oscurlo\ComponentRenderer\ComponentRenderer;

$renderer = new ComponentRenderer([
    'MyApp\\Components' => ['Button', 'Card'],
]);

$renderer->render('<Card title="Hello"><Button>Click me</Button></Card>');
```

## Examples

| # | What it shows |
|---|---|
| [example1.php](./examples/example1.php) | Multiple components — functions, namespaces, class methods |
| [example2.php](./examples/example2.php) | Output buffering with `start()` / `end()` |
| [example3.php](./examples/example3.php) | Templates with variables via `Component::template()` |
| [example4.php](./examples/example4.php) | Static API with `Component::render()` |
| [example5.php](./examples/example5.php) | `PropsCaster` — type-safe props (bool, int, array…) |
| [example6.php](./examples/example6.php) | Nested components + `Bootstrap::accordion` inside a card |

## Requirements

- PHP >= 8.0
- ext-dom
- ext-libxml

## Support

[![Buy me a coffee](https://www.buymeacoffee.com/assets/img/custom_images/yellow_img.png)](https://www.buymeacoffee.com/oscurlo)
[![Donate with PayPal](https://raw.githubusercontent.com/stefan-niedermann/paypal-donate-button/master/paypal-donate-button.png)](https://paypal.me/oscurlo?country.x=CO&locale.x=es_XC)
