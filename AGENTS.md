# AGENTS.md — Contao CokibanBundle

## Project Overview

Contao CMS bundle providing a cookie banner configured via `config.yml`.
Built with PHP 8.0+ / Symfony, Alpine.js frontend, esbuild build pipeline.

- **Type:** `contao-bundle`
- **Root namespace:** `tdoescher\CokibanBundle` (PSR-4 mapped to `src/`)
- **Contao version:** ^5.0
- **License:** LGPL-3.0-or-later

## Project Structure

```
src/
  CokibanBundle.php              # Bundle class
  ContaoManager/Plugin.php       # Contao Manager plugin registration
  DependencyInjection/           # Symfony DI Extension + Configuration TreeBuilder
  EventListener/                 # Contao hooks (parseFrontendTemplate, replaceInsertTags)
  Service/CokibanContext.php     # Core service — reads config, determines active banner
  Twig/AppExtension.php          # Twig extension (cokiban_wrapper_open/close, cokiban_replacement)
config/                          # Symfony service definitions (YAML)
contao/templates/                # Legacy .html5 + modern .html.twig templates
public/                          # Frontend assets (JS, SCSS, minified builds)
```

## Build / Lint / Test Commands

### JavaScript & CSS Build

```bash
npm run build          # Bundles JS (esbuild) + compiles SCSS -> public/cokiban.min.{js,css}
```

### JavaScript Linting

```bash
npx eslint .           # Run ESLint (no npm script defined — call directly)
npx eslint src/        # Lint specific directory
npx eslint public/cokiban.js  # Lint single file
```

### PHP

```bash
composer install        # Install dependencies
```

There is **no PHP test suite, no PHPUnit, no PHP-CS-Fixer, and no PHPStan** configured.
There is **no CI/CD pipeline**.

If adding tests in the future:
```bash
# Conventional commands for Contao bundles:
vendor/bin/phpunit                          # Run all tests
vendor/bin/phpunit tests/Path/ToTest.php    # Run single test file
vendor/bin/phpunit --filter testMethodName  # Run single test method
```

## Code Style Guidelines

### PHP

#### General

- **No `declare(strict_types=1)`** — not used in any file
- **PHP 8.0+** features: constructor property promotion, readonly properties, PHP attributes
- **No classes marked `final`**
- **No enums, match expressions, or union types** currently used

#### Formatting

- **Indentation:** 4 spaces
- **Brace style:** Opening brace on same line for classes/methods; Allman-style for `else`:
  ```php
  if (...) {
      ...
  }
  else {
      ...
  }
  ```
- **Short array syntax:** Always `[]`, never `array()`
- **Trailing commas:** Used in constructor parameters and multi-line arrays (inconsistent elsewhere)

#### Strings

- **Single quotes** for all strings by default
- **Double quotes** only for HTML output and regex patterns
- **String concatenation** with `.` operator (spaces around it), never variable interpolation

#### Imports

- One `use` statement per line, no group imports
- Roughly alphabetical order
- Fully qualified imports always (no relative imports)
- No blank-line grouping between framework/vendor/own imports

#### Naming Conventions

| Element            | Convention   | Example                          |
|--------------------|--------------|----------------------------------|
| Classes            | PascalCase   | `CokibanContext`                 |
| Methods            | camelCase    | `getConfig()`, `cokibanOpen()`   |
| Variables          | camelCase    | `$pageModel`, `$templateName`    |
| Config keys        | snake_case   | `disable_token`, `main_headline` |
| Properties (DI)    | camelCase    | `$cokibanConfig`                 |

#### Visibility & Properties

- **Injected dependencies:** `private readonly` via constructor promotion
- **Mutable state:** `protected` (e.g., `protected array $cokiban = []`)
- **All methods:** `public` (no private/protected methods in codebase)

#### Dependency Injection

- Constructor injection with PHP 8 property promotion
- Symfony attributes for configuration: `#[Autowire(param: 'cokiban')]`
- Contao hooks via attributes: `#[AsHook('replaceInsertTags', priority: 100)]`
- Services registered by FQCN in YAML with `autowire: true`, `autoconfigure: true`

#### Error Handling

- **No exceptions thrown or caught** — no try/catch blocks in PHP code
- **Early returns** for guard clauses (check for null, backend scope, empty config)
- **`return false`** as signal value in InsertTag listener when not responsible

#### Documentation

- **File header block** in every PHP file:
  ```php
  /**
   * This file is part of CokibanBundle for Contao
   *
   * @package     tdoescher/cokiban-bundle
   * @author      Torben Doescher <mail@tdoescher.de>
   * @license     LGPL
   * @copyright   tdoescher.de // WEB & IT <https://tdoescher.de>
   */
  ```
- **No method-level PHPDoc** — no `@param`, `@return`, `@throws` annotations
- **No inline comments**

#### Return Types

- Used on most methods but not all — some Twig helper methods and `__invoke` lack return types
- When adding new code, always include return type declarations

### JavaScript

ESLint is configured with these rules (see `eslint.config.js`):

| Rule                     | Value                |
|--------------------------|----------------------|
| `indent`                 | 4 spaces             |
| `semi`                   | Always               |
| `quotes`                 | Single quotes        |
| `comma-dangle`           | Always (multi-line)  |
| `arrow-parens`           | Always required      |
| `prefer-arrow-callback`  | Enforced             |

- **Target:** ES2022, Chrome 90+, Edge 90+, Firefox 90+, Safari 14+
- **Module type:** ESM (`"type": "module"` in package.json)
- Build drops `console` and `debugger` statements from minified output

### Templates

- Legacy Contao `.html5` templates coexist with modern `.html.twig` templates
- Twig templates are in `contao/templates/content_element/` and `contao/templates/page/`
- Base Twig template uses `_base.html.twig` / `_cokiban.html.twig` naming with underscore prefix for partials

## Contao-Specific Patterns

- **Hooks** are registered via PHP attributes (`#[AsHook(...)]`), not via `$GLOBALS` or YAML
- **InsertTags:** `cokiban`, `cokiban_open`, `cokiban_close` — handled in `ReplaceInsertTagsListener`
- **FrontendTemplate** usage: `new FrontendTemplate('template_name')` with `$objTemplate->parse()`
- **PageModel access:** Via `$request->attributes->get('pageModel')`, not global `$objPage`
- **Configuration:** Complex TreeBuilder in `Configuration.php` under the `cokiban` key
- **Body injection:** Regex-based injection after `<body>` tag in `ParseFrontendTemplateListener`
