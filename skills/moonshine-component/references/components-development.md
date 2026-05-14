# MoonShine Custom Components Development - AI Guidelines

This guide helps AI assistants create custom components for MoonShine admin panel. Components are UI building blocks for decorative and display purposes - they don't save data like fields do.

## Table of Contents

- [Overview](#overview)
- [Components vs Fields](#components-vs-fields)
- [Creating a Component](#creating-a-component)
- [Component Class Anatomy](#component-class-anatomy)
- [View Templates](#view-templates)
- [Fluent Methods](#fluent-methods)
- [Assets](#assets)
- [Slots and Nested Components](#slots-and-nested-components)
- [Common Patterns](#common-patterns)
- [Best Practices](#best-practices)

---

<a name="overview"></a>
## Overview

**MoonShine components** are reusable UI blocks for decorating and displaying content in the admin panel. Unlike fields, components:
- ❌ Don't save data
- ❌ Don't have modes (default/preview/raw)
- ✅ Are used for decoration and display
- ✅ Can contain other components
- ✅ Use `viewData()` just like fields

### File Locations

```
app/MoonShine/Components/
├── YourComponent.php          # Component class

resources/views/admin/components/
├── your-component.blade.php   # Component view template
```

---

<a name="components-vs-fields"></a>
## Components vs Fields

| Feature | Fields | Components |
|---------|--------|-----------|
| Purpose | Data input/output | UI decoration/display |
| Saves data | ✅ Yes | ❌ No |
| Has modes | ✅ Yes (default/preview/raw) | ❌ No |
| Base class | `Field` | `MoonShineComponent` |
| `viewData()` | ✅ Yes | ✅ Yes |
| Used in | Forms, Tables | Layouts, Pages, anywhere |
| Can contain children | ⚠️ Limited | ✅ Yes (slots, nested) |

---

<a name="creating-a-component"></a>
## Creating a Component

### Artisan Command

```bash
php artisan moonshine:component ComponentName
```

**What gets created:**
- `app/MoonShine/Components/ComponentName.php` - PHP class
- `resources/views/admin/components/component-name.blade.php` - Blade template

### Generated Stub

```php
<?php

declare(strict_types=1);

namespace App\MoonShine\Components;

use MoonShine\UI\Components\MoonShineComponent;

/**
 * @method static static make()
 */
final class ComponentName extends MoonShineComponent
{
    protected string $view = 'admin.components.component-name';

    public function __construct()
    {
        parent::__construct();
    }

    protected function viewData(): array
    {
        return [];
    }
}
```

---

<a name="component-class-anatomy"></a>
## Component Class Anatomy

### Essential Properties

```php
class Footer extends MoonShineComponent
{
    // View path (relative to resources/views)
    protected string $view = 'admin.components.footer';

    // Component properties
    protected array $menu = [];
    protected string|Closure $copyright = '';
}
```

### Constructor

Components can accept parameters in constructor:

```php
public function __construct(
    protected array $menu = [],
    protected string|Closure $copyright = ''
) {
    parent::__construct();
}
```

**Usage:**
```php
Footer::make()->copyright('© 2024 My Company')->menu([
    '/privacy' => 'Privacy Policy',
    '/terms' => 'Terms of Service',
])
```

### Essential Methods

#### 1. `viewData()` - Pass Data to View

**Purpose:** Send data from PHP class to Blade template.

**IMPORTANT:** Unlike fields, components DON'T have automatic system data. You control everything you pass.

```php
protected function viewData(): array
{
    return [
        'menu' => $this->getMenu(),
        'copyright' => $this->getCopyright(),
        'customData' => $this->customData,
    ];
}
```

**All data returned here becomes available in `@props` of your Blade template.**

#### 2. `prepareBeforeRender()` - Prepare Logic Before Rendering

**Purpose:** Process data, modify attributes, prepare component state BEFORE rendering. This is where business logic should live, NOT in Blade views!

**IMPORTANT:** Keep Blade views clean - move ALL logic to `prepareBeforeRender()`.

```php
protected function prepareBeforeRender(): void
{
    parent::prepareBeforeRender();

    // Example: Prepare image attributes
    if ($this->width) {
        $this->imageAttributes['width'] = $this->width;
    }

    if ($this->height) {
        $this->imageAttributes['height'] = $this->height;
    }

    if ($this->lazy) {
        $this->imageAttributes['loading'] = 'lazy';
    }

    // Example: Merge styles
    $style = $this->objectFit ? "object-fit: {$this->objectFit};" : '';

    if ($style) {
        $this->customAttributes([
            'style' => $style,
        ]);
    }

    // Example: Add Alpine.js initialization
    if ($this->isScrollable) {
        $this->customAttributes([
            'x-init' => "\$nextTick(() => \$el.querySelector('.active')?.scrollIntoView())",
        ]);
    }
}
```

**Common use cases:**
- Preparing attributes for the view
- Merging styles or classes
- Computing derived values
- Adding Alpine.js directives
- Conditional logic based on component state

**Real example from MoonShine Menu component:**
```php
protected function prepareBeforeRender(): void
{
    parent::prepareBeforeRender();

    if (! $this->isTop() && $this->isScrollTo()) {
        $this->customAttributes([
            'x-init' => "\$nextTick(() => \$el.querySelector('.menu-item._is-active')?.scrollIntoView())",
        ]);
    }

    if ($this->isTop()) {
        $this->items->topMode();
    }
}
```

**Best practice:**
```php
// ❌ BAD - Logic in Blade view
@php
    $imageAttributes = [];
    if ($width) {
        $imageAttributes['width'] = $width;
    }
    if ($height) {
        $imageAttributes['height'] = $height;
    }
@endphp

// ✅ GOOD - Logic in PHP class
protected function prepareBeforeRender(): void
{
    parent::prepareBeforeRender();

    if ($this->width) {
        $this->imageAttributes['width'] = $this->width;
    }

    if ($this->height) {
        $this->imageAttributes['height'] = $this->height;
    }
}
```

#### 3. Fluent Methods

Methods for configuring the component:

```php
public function copyright(string|Closure $text): static
{
    $this->copyright = $text;
    return $this;
}

public function menu(array $data): static
{
    $this->menu = $data;
    return $this;
}
```

**MUST return `static` for method chaining!**

#### 3. Getter Methods

Retrieve and format data:

```php
public function getCopyright(): string
{
    return value($this->copyright); // value() evaluates closures
}

public function getMenu(): Collection
{
    return new Collection($this->menu);
}
```

---

<a name="view-templates"></a>
## View Templates

### Basic Structure

```blade
@props([
    'menu' => [],
    'copyright' => '',
])

<footer {{ $attributes->merge(['class' => 'layout-footer']) }}>
    <div class="flex items-center justify-between">
        <div>{!! $copyright !!}</div>

        @if(!empty($menu))
            <nav>
                @foreach($menu as $link => $label)
                    <a href="{{ $link }}">{{ $label }}</a>
                @endforeach
            </nav>
        @endif
    </div>
</footer>
```

**Key points:**
- ✅ Use `@props` to declare expected data from `viewData()`
- ✅ Always add `{{ $attributes }}` to root element
- ✅ Use `{!! !!}` for HTML content, `{{ }}` for escaped text
- ✅ Provide default values in `@props`

### Using `{{ $attributes }}`

**CRITICAL:** Always add `{{ $attributes }}` to enable customization:

```blade
<!-- ✅ CORRECT -->
<div {{ $attributes->merge(['class' => 'my-component']) }}>
    Content
</div>

<!-- ❌ WRONG - Can't be customized! -->
<div class="my-component">
    Content
</div>
```

**Why this matters:**
- Allows adding classes from PHP: `Component::make()->customAttributes(['class' => 'custom'])`
- Enables data attributes: `Component::make()->customAttributes(['data-id' => '123'])`
- Proper MoonShine integration

---

<a name="fluent-methods"></a>
## Fluent Methods

### Creating Fluent Methods

Fluent methods allow method chaining for configuration:

```php
class Alert extends MoonShineComponent
{
    protected string $type = 'info';
    protected string $message = '';
    protected bool $dismissible = false;

    public function type(string $type): static
    {
        $this->type = $type;
        return $this; // ← MUST return $this
    }

    public function message(string $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function dismissible(bool $dismissible = true): static
    {
        $this->dismissible = $dismissible;
        return $this;
    }
}
```

**Usage:**
```php
Alert::make()
    ->type('warning')
    ->message('Be careful!')
    ->dismissible()
```

### Common Fluent Patterns

```php
// Toggle feature
public function withIcon(): static
{
    $this->hasIcon = true;
    return $this;
}

// Set variant
public function variant(string $variant): static
{
    $this->variant = $variant;
    return $this;
}

// Set content
public function title(string $title): static
{
    $this->title = $title;
    return $this;
}

// Closure support
public function text(string|Closure $text): static
{
    $this->text = $text;
    return $this;
}
```

### Using `value()` Helper

For properties that accept closures:

```php
use function MoonShine\UI\Components\Layout\value;

public function getCopyright(): string
{
    // value() evaluates closures, returns as-is for strings
    return value($this->copyright);
}
```

---

<a name="assets"></a>
## Assets

Components can include CSS/JS assets:

**IMPORTANT:** The `assets()` method MUST be `protected`, not `public`.

```php
use MoonShine\AssetManager\Css;
use MoonShine\AssetManager\Js;

class Chart extends MoonShineComponent
{
    protected string $view = 'admin.components.chart';

    protected function assets(): array
    {
        return [
            Css::make('/css/chart.css'),
            Js::make('/js/chart.js'),
        ];
    }
}
```

Store assets in `public/` directory:
```
public/
├── css/
│   └── chart.css
└── js/
    └── chart.js
```

---

<a name="slots-and-nested-components"></a>
## Slots and Nested Components

### Using Default Slot

```blade
@props(['title' => ''])

<div {{ $attributes }}>
    <h3>{{ $title }}</h3>
    {{ $slot }}
</div>
```

**Usage:**
```php
Card::make()->title('My Card')->content([
    Text::make('Content goes here')
])
```

### Named Slots

```blade
@props(['title' => ''])

<div {{ $attributes }}>
    <header>
        {{ $header ?? '' }}
    </header>

    <div class="body">
        {{ $slot }}
    </div>

    <footer>
        {{ $footer ?? '' }}
    </footer>
</div>
```

### Nested Components via `AbstractWithComponents`

For components that contain other components, extend `AbstractWithComponents`:

```php
use MoonShine\UI\Components\AbstractWithComponents;
use MoonShine\Contracts\UI\ComponentContract;

class Footer extends AbstractWithComponents
{
    protected string $view = 'admin.components.footer';

    /**
     * @param  iterable<array-key, ComponentContract>  $components
     */
    public function __construct(
        iterable $components = [],
        protected array $menu = []
    ) {
        parent::__construct($components);
    }

    protected function viewData(): array
    {
        return [
            'menu' => $this->menu,
        ];
    }
}
```

**In Blade:**
```blade
@props([
    'components' => [],
    'menu' => []
])

<footer {{ $attributes }}>
    <!-- Render nested components -->
    <x-moonshine::components :components="$components" />

    {{ $slot ?? '' }}

    <nav>
        @foreach($menu as $link => $label)
            <a href="{{ $link }}">{{ $label }}</a>
        @endforeach
    </nav>
</footer>
```

**Usage:**
```php
Footer::make([
    Alert::make()->message('Hello'),
    Button::make('Click me'),
])
->menu([
    '/privacy' => 'Privacy',
    '/terms' => 'Terms',
])
```

---

<a name="common-patterns"></a>
## Common Patterns

### Alert Component

```php
<?php

namespace App\MoonShine\Components;

use MoonShine\UI\Components\MoonShineComponent;

class Alert extends MoonShineComponent
{
    protected string $view = 'admin.components.alert';

    public function __construct(
        protected string $type = 'info',
        protected string $message = '',
        protected bool $dismissible = false
    ) {
        parent::__construct();
    }

    public function type(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function message(string $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function dismissible(bool $dismissible = true): static
    {
        $this->dismissible = $dismissible;
        return $this;
    }

    protected function viewData(): array
    {
        return [
            'type' => $this->type,
            'message' => $this->message,
            'dismissible' => $this->dismissible,
        ];
    }
}
```

**Blade:**
```blade
@props([
    'type' => 'info',
    'message' => '',
    'dismissible' => false,
])

<div
    {{ $attributes->merge(['class' => "alert alert-{$type}"]) }}
    @if($dismissible) x-data="{ show: true }" x-show="show" @endif
>
    <div class="alert-content">
        {!! $message !!}
    </div>

    @if($dismissible)
        <button @click="show = false" class="alert-close">×</button>
    @endif
</div>
```

**Usage:**
```php
Alert::make()
    ->type('warning')
    ->message('This is a warning!')
    ->dismissible()
```

### Stats Card Component

```php
<?php

namespace App\MoonShine\Components;

use MoonShine\UI\Components\MoonShineComponent;
use Closure;

class StatsCard extends MoonShineComponent
{
    protected string $view = 'admin.components.stats-card';

    public function __construct(
        protected string|Closure $label = '',
        protected string|Closure|int $value = 0,
        protected string $icon = '',
        protected string $color = 'primary'
    ) {
        parent::__construct();
    }

    public function label(string|Closure $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function value(string|Closure|int $value): static
    {
        $this->value = $value;
        return $this;
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;
        return $this;
    }

    public function color(string $color): static
    {
        $this->color = $color;
        return $this;
    }

    protected function viewData(): array
    {
        return [
            'label' => value($this->label),
            'value' => value($this->value),
            'icon' => $this->icon,
            'color' => $this->color,
        ];
    }
}
```

**Blade:**
```blade
@props([
    'label' => '',
    'value' => 0,
    'icon' => '',
    'color' => 'primary',
])

<div {{ $attributes->merge(['class' => 'stats-card']) }}>
    <div class="stats-card-icon bg-{{ $color }}">
        <x-moonshine::icon :icon="$icon" />
    </div>

    <div class="stats-card-content">
        <div class="stats-card-value">{{ $value }}</div>
        <div class="stats-card-label">{{ $label }}</div>
    </div>
</div>
```

**Usage:**
```php
StatsCard::make()
    ->label('Total Users')
    ->value(fn() => User::count())
    ->icon('users')
    ->color('success')
```

### Breadcrumbs Component

```php
<?php

namespace App\MoonShine\Components;

use MoonShine\UI\Components\MoonShineComponent;

class Breadcrumbs extends MoonShineComponent
{
    protected string $view = 'admin.components.breadcrumbs';

    public function __construct(
        protected array $items = []
    ) {
        parent::__construct();
    }

    public function items(array $items): static
    {
        $this->items = $items;
        return $this;
    }

    protected function viewData(): array
    {
        return [
            'items' => $this->items,
        ];
    }
}
```

**Blade:**
```blade
@props([
    'items' => [],
])

<nav {{ $attributes->merge(['class' => 'breadcrumbs']) }}>
    @foreach($items as $url => $label)
        @if($loop->last)
            <span class="breadcrumb-current">{{ $label }}</span>
        @else
            <a href="{{ $url }}" class="breadcrumb-link">{{ $label }}</a>
            <span class="breadcrumb-separator">/</span>
        @endif
    @endforeach
</nav>
```

**Usage:**
```php
Breadcrumbs::make()->items([
    '/' => 'Home',
    '/users' => 'Users',
    '#' => 'Edit User',
])
```

---

<a name="best-practices"></a>
## Best Practices

### 1. Always Return `static` in Fluent Methods

```php
// ✅ CORRECT
public function title(string $title): static
{
    $this->title = $title;
    return $this;
}

// ❌ WRONG
public function title(string $title): void
{
    $this->title = $title;
}
```

### 2. Always Add `{{ $attributes }}`

```blade
<!-- ✅ CORRECT -->
<div {{ $attributes->merge(['class' => 'component']) }}>
    Content
</div>

<!-- ❌ WRONG -->
<div class="component">
    Content
</div>
```

### 3. Move Logic to `prepareBeforeRender()`

```php
// ❌ BAD - Logic in Blade
@php
    $attrs = [];
    if ($width) $attrs['width'] = $width;
    if ($height) $attrs['height'] = $height;
@endphp

// ✅ GOOD - Logic in PHP class
protected function prepareBeforeRender(): void
{
    parent::prepareBeforeRender();

    if ($this->width) {
        $this->imageAttributes['width'] = $this->width;
    }

    if ($this->height) {
        $this->imageAttributes['height'] = $this->height;
    }
}
```

### 4. Use `value()` for Closure Support

```php
use function MoonShine\UI\Components\Layout\value;

// ✅ CORRECT - Supports closures
protected function viewData(): array
{
    return [
        'text' => value($this->text),
    ];
}

// ❌ WRONG - Closures won't work
protected function viewData(): array
{
    return [
        'text' => $this->text,
    ];
}
```

### 5. Provide Default Values in `@props`

```blade
<!-- ✅ CORRECT -->
@props([
    'title' => '',
    'items' => [],
    'color' => 'primary',
])

<!-- ❌ WRONG - No defaults -->
@props([
    'title',
    'items',
    'color',
])
```

### 6. Use Type Hints

```php
// ✅ CORRECT
public function title(string $title): static
{
    $this->title = $title;
    return $this;
}

protected function viewData(): array
{
    return ['title' => $this->title];
}

// ❌ WRONG
public function title($title)
{
    $this->title = $title;
    return $this;
}
```

### 7. Handle Empty States

```blade
@props(['items' => []])

<div {{ $attributes }}>
    @if(!empty($items))
        @foreach($items as $item)
            <div>{{ $item }}</div>
        @endforeach
    @else
        <div class="empty-state">No items found</div>
    @endif
</div>
```

### 8. Use Proper HTML Escaping

```blade
<!-- For user input - ESCAPE -->
<div>{{ $userInput }}</div>

<!-- For HTML content from trusted source - DON'T ESCAPE -->
<div>{!! $trustedHtml !!}</div>
```

### 9. Support Alpine.js for Interactivity

```blade
<div
    {{ $attributes }}
    x-data="{ open: false }"
>
    <button @click="open = !open">Toggle</button>

    <div x-show="open">
        Content
    </div>
</div>
```

---

## Component Stub Template

When you run `php artisan moonshine:component`, this is the stub used:

```php
<?php

declare(strict_types=1);

namespace {namespace};

use MoonShine\UI\Components\MoonShineComponent;

/**
 * @method static static make()
 */
final class DummyClass extends MoonShineComponent
{
    protected string $view = '{view}';

    public function __construct()
    {
        parent::__construct();
    }

    protected function viewData(): array
    {
        return [];
    }
}
```

**Placeholders:**
- `{namespace}` - Component namespace (e.g., `App\MoonShine\Components`)
- `{view}` - View path (e.g., `admin.components.your-component`)
- `DummyClass` - Your component name

---

## Summary

**Key Takeaways:**

1. Components are for **decoration and display**, not data saving
2. Extend `MoonShineComponent` or `AbstractWithComponents`
3. Use `viewData()` to pass data to Blade
4. **Fluent methods MUST return `static`**
5. **Always add `{{ $attributes }}`** to root element
6. Use `value()` helper for closure support
7. The `assets()` method MUST be `protected`
8. Provide default values in `@props`
9. Support slots for flexibility
10. Files go in:
    - `app/MoonShine/Components/` (PHP)
    - `resources/views/admin/components/` (Blade)
