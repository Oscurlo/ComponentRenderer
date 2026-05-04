# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [v1.2.0] — 2026-05-04

### Added

- **`src/Support/HtmlHelper.php`** — new standalone utility class with three static methods:
  - `HtmlHelper::wrap($html, $encoding)` — wraps an HTML fragment in a minimal document so `DOMDocument` can parse it reliably.
  - `HtmlHelper::unwrap($html)` — extracts the inner `<body>` content from a full HTML document.
  - `HtmlHelper::isDocument($html)` — returns `true` if the string contains a full `<html>…</html>` document.
- **`src/PropsCaster.php`** — renamed and promoted class for type-casting HTML attribute strings into proper PHP types (`bool`, `int`, `float`, `double`, `string`, `array`, `object`). Replaces `ComponentInterface` as the canonical name.
- **`src/ComponentRegistry.php`** — extracted from `ComponentManager`. Owns all component registration and resolution logic: `set_component_manager()`, `get_component_manager()`, `register_component()`, `component_exists()`, `check()`, `valid_tag()`, `convert_to_valid_tag()`, `get_file()`, `valid_name_function()`.
- **`src/Contracts/RendererInterface.php`** — public contract that `ComponentRenderer` now implements. Defines `set_component_manager()`, `get_component_manager()`, and `render()`.
- **`src/Concerns/HandlesDom.php`** — trait providing DOM state (`$dom`, `$dom_version`, `$dom_encoding`) and `get_params()`. Used by `ComponentExecutor` and `ComponentInterpreter`.
- **`src/Concerns/HandlesAttributes.php`** — trait providing `get_attributes()` and `extract_attributes()`. Used by `ComponentManager`.
- **`src/Concerns/RendersOutput.php`** — trait providing `print()`, `start()`, and `end()`. Used by `ComponentBuffer` and `ComponentRenderer`.
- **`tests/`** — first test suite for the project:
  - `tests/Unit/HtmlHelperTest.php` — 11 tests covering `wrap()`, `unwrap()`, `isDocument()`, and the wrap→unwrap roundtrip.
  - `tests/Unit/PropsCasterTest.php` — 15 tests covering all supported cast types, edge cases, missing props, and backwards-compatibility with `ComponentInterface`.
  - `tests/Feature/RenderTest.php` — 12 tests covering all three rendering modes, nested components, class-based components, and `Component::template()`.
  - `tests/bootstrap.php` — manual PSR-4 autoloader for running tests without Composer in CI environments.
- **`phpunit.xml`** — PHPUnit configuration with `Unit` and `Feature` test suites.
- **`examples/example4.php`** — demonstrates the static `Component::render()` API.
- **`examples/example5.php`** — demonstrates `PropsCaster` with `int`, `bool`, and `array` props.
- **`examples/example6.php`** — demonstrates nested components (`Bootstrap::accordion` inside `Bootstrap::card`) and JSON props.
- **`composer.json`**: added `scripts` section (`test`, `test:unit`, `test:feature`).
- **`composer.json`**: added `Oscurlo\\ComponentRenderer\\Tests\\` to `autoload-dev`.

### Changed

- **Minimum PHP version raised from `7.3` to `8.0`** — unlocks `str_contains()`, `str_starts_with()`, `match` expressions, `mixed` type hint, named arguments, and union types across the codebase.
- **`ComponentManager`** — now extends `ComponentRegistry` and uses the three new traits instead of defining everything inline. Reduced from ~200 lines to ~50 lines of real logic.
- **`ComponentExecutor`** — now extends `ComponentRegistry` directly (not `ComponentManager`). Uses `HandlesDom` trait. Execution logic split into `executeFromReference()` and `executeFromDirectory()` private methods for clarity. `execute_component()` now returns `bool` indicating whether the component was rendered.
- **`ComponentInterpreter`** — now uses `HtmlHelper` directly instead of the `ComponentManager` proxy methods. The processing loop is now `do/while` to handle components that render other components (recursive resolution). `getTagsForComponent()` snapshots the node list before iterating to prevent DOM mutation issues.
- **`ComponentBuffer`** — reduced to a single `use RendersOutput` declaration; `start()` and `end()` are now provided entirely by the trait.
- **`ComponentRenderer`** — now `implements RendererInterface`. Constructor accepts `?array $components = null`.
- **`autoload`** — `examples/components/Functions.php` moved from `autoload.files` to `autoload-dev.files`, fixing a bug where example functions were loaded into every consumer project.
- **`composer.json`** — `php` constraint updated from `>=7.3` to `>=8.0`.
- **README** — fully rewritten in both English and Spanish, covering all three rendering modes, `PropsCaster`, `HtmlHelper`, `ComponentManager` helpers, template syntax, project structure, and all 6 examples.

### Fixed

- **`ComponentManager::$check_storage`** — was declared `private array $check_storage;` without initialization, causing `Typed property must not be accessed before initialization` on PHP 8.x. Now initialized to `[]`.
- **`ComponentExecutor::$included_files`** — same uninitialized typed property issue. Now initialized to `[]`.
- **`ComponentManager::valid_tag()`** — used `strpos($component, $name)` which returns `0` (falsy) when the tag starts with `"component-"`, silently skipping the early-return guard. Fixed with `str_contains()`.
- **`ComponentManager::get_body()`** — `$bodyContent ??= $html` never executed because `$bodyContent` was initialized as `""` (not `null`). Fixed with an explicit `!== ""` comparison.
- **`ComponentBuffer::end()` / `Component::template()`** — `ob_get_clean()` can return `false` in PHP 8.x when no buffer is active. Now cast explicitly to `string`.
- **`Component::template()`** — removed unused variable `$all` from the `preg_replace_callback` destructure. Removed unnecessary by-reference assignment in the `$trim` closure.
- **`ComponentInterface::boolean()`** — added `mixed` type hint (PHP 8.0+).
- **`HtmlHelper::unwrap()`** — added early return for empty string input to prevent `ValueError: DOMDocument::loadHTML(): Argument #1 ($source) must not be empty` on PHP 8.x.
- **`@return false`** docblock in `contains_html_base()` corrected to `@return bool`.

### Deprecated

- **`ComponentInterface`** — use `PropsCaster` instead. `ComponentInterface` now extends `PropsCaster` as a transparent alias and will be removed in `v2.0.0`.
- **`ComponentManager::html_base()`** — use `HtmlHelper::wrap()` instead.
- **`ComponentManager::get_body()`** — use `HtmlHelper::unwrap()` instead.
- **`ComponentManager::contains_html_base()`** — use `HtmlHelper::isDocument()` instead.

---

## [v1.1.9] — previous release

See [Packagist](https://packagist.org/packages/oscurlo/component-renderer) for earlier release history.
