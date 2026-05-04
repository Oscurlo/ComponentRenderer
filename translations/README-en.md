# Component Renderer

Component Renderer is a lightweight PHP library for rendering reusable HTML components, inspired by JSX. It is built on top of PHP's native `DOMDocument` — no build step, no transpiler, no heavy framework required.

## Installation

```bash
composer require oscurlo/component-renderer
```

## Requirements

- PHP >= 8.0
- ext-dom
- ext-libxml

---

## Quick start

```php
use Oscurlo\ComponentRenderer\ComponentRenderer;

require __DIR__ . '/vendor/autoload.php';

$renderer = new ComponentRenderer([
    'MyApp\\Components' => ['Card', 'Button'],
]);

$renderer->render(<<<'HTML'
<Card title="Hello world">
    <Button label="Click me" />
</Card>
HTML);
```

---

## Registering components

Pass a component map to `ComponentRenderer`, `Component::render()`, or `set_component_manager()`.

```php
$components = [
    // Directory — resolves MyComponent.php inside that folder
    __DIR__ . '/components' => ['MyComponent'],

    // Namespace — resolves namespace\FunctionName()
    'MyApp\\Components' => ['Header', 'Footer', 'Button'],

    // Class — resolves MyClass::methodName()
    MyApp\UI\Bootstrap::class => ['card', 'accordion'],
];
```

The three supported registration types:

| Type | Key | Value |
|---|---|---|
| Directory | Absolute path to a folder | Component file name(s) (without `.php`) |
| Namespace | PHP namespace string | Function name(s) inside that namespace |
| Class | `ClassName::class` | Static method name(s) |

---

## Three rendering modes

### 1. Instance — `ComponentRenderer::render()`

Best for full-page rendering. Create the renderer once, reuse it across the request.

```php
use Oscurlo\ComponentRenderer\ComponentRenderer;

$renderer = new ComponentRenderer([
    'MyApp\\Components' => ['Layout', 'Card'],
]);

$renderer->render('<Layout><Card title="Hello" /></Layout>');
```

### 2. Buffer — `start()` / `end()`

Best when your HTML is spread across a PHP file with mixed output.

```php
$renderer->start();
?>
<Layout>
    <Card title="Hello">
        <p>Content here</p>
    </Card>
</Layout>
<?php
$renderer->end();
```

### 3. Static — `Component::render()`

Best for one-off renders or helper functions. Returns the HTML string instead of printing it.

```php
use Oscurlo\ComponentRenderer\Component;

$html = Component::render(
    html: '<Card title="Hello"><p>Content</p></Card>',
    components: ['MyApp\\Components' => ['Card']],
);

echo $html;
```

---

## Templates — `Component::template()`

Renders a PHP/HTML file with a lightweight `{{ }}` syntax compiled on the fly.

```php
use Oscurlo\ComponentRenderer\Component;

$html = Component::template(
    filename: __DIR__ . '/views/page.php',
    vars: ['title' => 'Hello', 'items' => $items],
    props: $props,  // optional — available as $props inside the template
);
```

### Template syntax

| Syntax | Compiled to | Use for |
|---|---|---|
| `{{ $variable }}` | `<?= $variable ?>` | Output a value |
| `{{ @php code }}` | `<?php php code ?>` | Execute PHP (loops, conditions…) |

```html
<!-- views/page.php -->
<h1>{{ $title }}</h1>

<ul>
    {{ @foreach ($items as $item): }}
        <li>{{ $item['name'] }}</li>
    {{ @endforeach }}
</ul>

{{ @if ($props->showFooter): }}
    <footer>Done</footer>
{{ @endif }}
```

---

## Writing components

A component is a **function** or **static class method** that receives a `$props` object and returns an HTML string.

### Function component

```php
namespace MyApp\Components;

use Oscurlo\ComponentRenderer\ComponentManager;

function Card(object $props): string
{
    $props->title ??= '';
    $attrs = ComponentManager::get_attributes($props, exclude: ['title']);

    return <<<HTML
    <div class="card" {$attrs}>
        <div class="card-header">{$props->title}</div>
        <div class="card-body">{$props->children}</div>
    </div>
    HTML;
}
```

### Class component (static method)

```php
namespace MyApp\UI;

use Oscurlo\ComponentRenderer\Component;

class Layout
{
    public static function page(object $props): string
    {
        $props->title ??= 'Untitled';

        return Component::template(__DIR__ . '/layout.php', props: $props);
    }
}
```

Register it as:

```php
MyApp\UI\Layout::class => ['page']
```

Use it as:

```html
<Layout::page title="Home">
    <p>Page content here</p>
</Layout::page>
```

---

## Props

Every attribute on a component tag becomes a property of the `$props` object.

| Prop | Description |
|---|---|
| `$props->children` | Inner HTML content of the tag |
| `$props->textContent` | Plain text content (no child tags) |
| `$props->anyAttribute` | Any other attribute passed on the tag |

```html
<Button type="submit" disabled="true">Save</Button>
```

```php
function Button(object $props): string
{
    // $props->type        → "submit"  (string)
    // $props->disabled    → "true"    (string — use PropsCaster to cast)
    // $props->children    → "Save"
    // $props->textContent → "Save"
}
```

---

## PropsCaster — type-safe props

HTML attributes are always strings. Use `PropsCaster::interface()` to cast them to the correct PHP type.

```php
use Oscurlo\ComponentRenderer\PropsCaster;

function ProgressBar(object $props): string
{
    PropsCaster::interface([
        'value'   => 'int',
        'max'     => 'int',
        'striped' => 'bool',
        'animate' => 'bool',
    ], $props);

    // $props->value   → 75     (int)
    // $props->striped → true   (bool)
    // ...
}
```

Supported types:

| Type string | PHP cast |
|---|---|
| `bool` / `boolean` | `filter_var()` — handles `"true"`, `"false"`, `"1"`, `"0"` |
| `int` / `integer` | `intval()` |
| `float` | `floatval()` |
| `double` | `doubleval()` |
| `string` / `str` | `(string)` |
| `array` | `json_decode($value, true)` |
| `object` | `json_decode($value)` |

---

## Helper methods — `ComponentManager`

| Method | Description |
|---|---|
| `ComponentManager::get_attributes($props, $exclude)` | Build an HTML attribute string from props, skipping `children`, `textContent`, and any extra keys you exclude |
| `ComponentManager::extract_attributes($props, $columns)` | Build an attribute string from only the listed columns |
| `ComponentManager::print(...$strings)` | Echo strings separated by a space |

```php
function Input(object $props): string
{
    $props->id ??= uniqid();
    $props->{'label-text'} ??= '';

    // Builds: id="..." type="..." class="..." (excludes label-text, children, textContent)
    $attrs = ComponentManager::get_attributes($props, exclude: ['label-text']);

    return <<<HTML
    <label for="{$props->id}">{$props->{'label-text'}}</label>
    <input {$attrs}>
    HTML;
}
```

---

## `HtmlHelper` — internal HTML utilities

Available at `Oscurlo\ComponentRenderer\Support\HtmlHelper`.

| Method | Description |
|---|---|
| `HtmlHelper::wrap($html, $encoding)` | Wraps a fragment in a minimal HTML document so DOMDocument can parse it |
| `HtmlHelper::unwrap($html)` | Extracts the `<body>` content from a full HTML document |
| `HtmlHelper::isDocument($html)` | Returns `true` if the string contains a full `<html>...</html>` document |

---

## Examples

| File | What it shows |
|---|---|
| [example1.php](../examples/example1.php) | Multiple components — functions, namespaces, class methods |
| [example2.php](../examples/example2.php) | Output buffering with `start()` / `end()` |
| [example3.php](../examples/example3.php) | Templates with variables via `Component::template()` |
| [example4.php](../examples/example4.php) | Static API with `Component::render()` |
| [example5.php](../examples/example5.php) | `PropsCaster` — type-safe props (bool, int, array…) |
| [example6.php](../examples/example6.php) | Nested components + `Bootstrap::accordion` inside a card |

---

## Project structure

```
src/
├── Contracts/
│   └── RendererInterface.php    # Public contract for renderers
├── Concerns/
│   ├── HandlesDom.php           # DOM state + get_params()
│   ├── HandlesAttributes.php    # get_attributes(), extract_attributes()
│   └── RendersOutput.php        # print(), start(), end()
├── Support/
│   └── HtmlHelper.php           # wrap(), unwrap(), isDocument()
├── PropsCaster.php              # Type casting for props
├── ComponentInterface.php       # @deprecated alias for PropsCaster
├── ComponentRegistry.php        # Component registration + resolution
├── ComponentManager.php         # Facade — wires all concerns together
├── ComponentExecutor.php        # Executes a single component
├── ComponentInterpreter.php     # Parses HTML and drives execution
├── ComponentBuffer.php          # start() / end() buffering
├── ComponentRenderer.php        # Main entry point (instance API)
└── Component.php                # Static entry point
```

---

## Contributing

1. Fork the repository.
2. Create a new branch: `git checkout -b feature/my-feature`
3. Make your changes and add tests.
4. Run the test suite: `composer test`
5. Open a Pull Request.

---

## Support

If you find this library useful, you can support it:

[![Buy me a coffee](https://www.buymeacoffee.com/assets/img/custom_images/yellow_img.png)](https://www.buymeacoffee.com/oscurlo)
[![Donate with PayPal](https://raw.githubusercontent.com/stefan-niedermann/paypal-donate-button/master/paypal-donate-button.png)](https://paypal.me/oscurlo?country.x=CO&locale.x=es_XC)
