# MoonShine Custom Fields Development - AI Guidelines

This guide helps AI assistants create custom fields for MoonShine admin panel. Fields are the building blocks of forms and tables in MoonShine.

## Table of Contents

- [Overview](#overview)
- [Field Structure](#field-structure)
- [Creating a Field](#creating-a-field)
- [Field Class Anatomy](#field-class-anatomy)
- [View Templates](#view-templates)
- [Key Methods Reference](#key-methods-reference)
- [Fluent Methods](#fluent-methods)
- [Field Modes](#field-modes)
- [Relationship Fields](#relationship-fields)
- [Assets (CSS/JS)](#assets)
- [Common Patterns](#common-patterns)
- [Best Practices](#best-practices)

---

<a name="overview"></a>
## Overview

**MoonShine fields** consist of two parts:
1. **PHP Class** - Logic, data handling, state management
2. **Blade View** - HTML template for rendering

Fields work in two main contexts:
- **FormBuilder** - Interactive inputs for data entry
- **TableBuilder** - Read-only preview for data display

### File Locations

```
app/MoonShine/Fields/
├── YourField.php              # Field class

resources/views/admin/fields/
├── your-field.blade.php       # Field view template
```

---

<a name="field-structure"></a>
## Field Structure

### Artisan Command

MoonShine provides a command to generate field scaffolding:

```bash
php artisan moonshine:field FieldName
```

**Interactive Options:**
1. **Namespace**: Default `App\MoonShine\Fields`, can customize
2. **Extends**: Choose base field to extend (Text, Textarea, Select, etc.)
3. **View**: Auto-generates view path

**What gets created:**
- `app/MoonShine/Fields/FieldName.php` - PHP class
- `resources/views/admin/fields/field-name.blade.php` - Blade template

---

<a name="creating-a-field"></a>
## Creating a Field

### Step 1: Generate Skeleton

```bash
php artisan moonshine:field Preview
```

**Choose parent field:**
- `Field` - Base field (most flexible)
- `Text` - Extends text input
- `Textarea` - Extends textarea
- `Select` - Extends select/dropdown
- `Image` - Extends image field
- And more...

### Step 2: Understand the Generated Code

```php
<?php

declare(strict_types=1);

namespace App\MoonShine\Fields;

use {ParentField};
use Closure;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use Illuminate\Contracts\Support\Renderable;

class Preview extends {ParentFieldShort}
{
    protected string $view = 'admin.fields.preview';

    protected function reformatFilledValue(mixed $data): mixed
    {
        return parent::reformatFilledValue($data);
    }

    protected function prepareFill(array $raw = [], ?DataWrapperContract $casted = null, int $index = 0): mixed
    {
        return parent::prepareFill($raw, $casted, $index);
    }

    protected function resolveValue(): mixed
    {
        return $this->toValue();
    }

    protected function resolvePreview(): Renderable|string
    {
        return (string) ($this->toFormattedValue() ?? '');
    }

    protected function resolveOnApply(): ?Closure
    {
        return function (mixed $item): mixed {
            return data_set($item, $this->getColumn(), $this->getRequestValue());
        };
    }

    protected function viewData(): array
    {
        return [
            //
        ];
    }
}
```

---

<a name="field-class-anatomy"></a>
## Field Class Anatomy

### Essential Properties

```php
class YourField extends Field
{
    // View path (relative to resources/views)
    protected string $view = 'admin.fields.your-field';

    // Custom properties for fluent methods
    protected bool $isHighlighted = false;
    protected string $variant = 'default';
}
```

### Essential Methods

#### 1. `viewData()` - Pass Data to View

**Purpose:** Send ADDITIONAL data from PHP class to Blade template.

**IMPORTANT:** The base `Field` class already provides these via `systemViewData()`:
- `value` - Field value (via `$this->getValue()`)
- `attributes` - HTML attributes
- `label` - Field label
- `column` - Database column name
- `errors` - Validation errors

You DON'T need to pass these - they're always available!

```php
protected function viewData(): array
{
    return [
        // ❌ DON'T do this - 'value' is already available!
        // 'value' => $this->toValue(),

        // ✅ DO this - pass ADDITIONAL custom data
        'isHighlighted' => $this->isHighlighted,
        'variant' => $this->variant,
        'maxStars' => $this->maxStars,
        'apiKey' => $this->apiKey,
    ];
}
```

**What you return here becomes available in `@props` of your Blade template, PLUS the system data.**

#### 2. `resolveValue()` - Get Field Value for Form

**Purpose:** Determine what value to display in the input field.

```php
protected function resolveValue(): mixed
{
    // Default mode (forms)
    return $this->toValue();
}
```

**Common patterns:**
```php
// Simple value
protected function resolveValue(): mixed
{
    return $this->toValue();
}

// Transform value before display
protected function resolveValue(): mixed
{
    $value = $this->toValue();
    return $value ? json_decode($value, true) : [];
}

// Preview mode (read-only, used in tables)
protected function resolveValue(): mixed
{
    return $this->preview(); // Switch to preview rendering
}
```

#### 3. `resolvePreview()` - Display Value in Tables

**Purpose:** How the field renders in TableBuilder (read-only mode).

```php
use MoonShine\UI\Components\Boolean;

protected function resolvePreview(): Renderable|string
{
    $value = $this->toFormattedValue();

    // Return component
    return Boolean::make((bool) $value)->render();

    // Or return string
    return (string) $value;
}
```

**Examples:**

```php
// Display as badge
use MoonShine\UI\Components\Badge;

protected function resolvePreview(): Renderable|string
{
    return Badge::make($this->toFormattedValue())->render();
}

// Display as image
use MoonShine\UI\Components\Thumbnails;

protected function resolvePreview(): Renderable|string
{
    return Thumbnails::make($this->toValue())->render();
}

// Custom HTML string
protected function resolvePreview(): Renderable|string
{
    $value = $this->toFormattedValue();
    return "<strong class='text-primary'>{$value}</strong>";
}
```

#### 4. `resolveOnApply()` - Save Field Value

**Purpose:** Handle form submission and modify the model.

**Returns:** Closure that receives `$item` (model) and must return modified `$item`.

```php
protected function resolveOnApply(): ?Closure
{
    return function (mixed $item): mixed {
        // Get submitted value
        $value = $this->getRequestValue();

        // Set it on the model
        data_set($item, $this->getColumn(), $value);

        // Must return the item
        return $item;
    };
}
```

**Important Notes:**
- `$item` is the Eloquent model being saved
- You MUST return `$item` at the end
- Access request value via `$this->getRequestValue()`
- Access field column via `$this->getColumn()`

**Advanced examples:**

```php
// Transform before saving
protected function resolveOnApply(): ?Closure
{
    return function (mixed $item): mixed {
        $value = $this->getRequestValue();

        // Convert array to JSON
        data_set($item, $this->getColumn(), json_encode($value));

        return $item;
    };
}

// Skip if no value
protected function resolveOnApply(): ?Closure
{
    return function (mixed $item): mixed {
        $value = $this->getRequestValue();

        if ($value !== null && $value !== '') {
            data_set($item, $this->getColumn(), $value);
        }

        return $item;
    };
}

// Disable saving (read-only field)
protected function resolveOnApply(): ?Closure
{
    return static fn($item) => $item;
}

public function isCanApply(): bool
{
    return false; // This field doesn't save
}
```

#### 5. `resolveOnBeforeApply()` - Before Save Hook

**Purpose:** Run logic BEFORE the model is saved (before `resolveOnApply`).

```php
protected function resolveOnBeforeApply(): ?Closure
{
    return function (mixed $item): mixed {
        // Validate or prepare data
        $value = $this->getRequestValue();

        if (empty($value)) {
            throw new \Exception('This field is required');
        }

        return $item;
    };
}
```

#### 6. `resolveOnAfterApply()` - After Save Hook

**Purpose:** Run logic AFTER the model is saved. Used for relationships or side effects.

```php
protected function resolveOnAfterApply(): ?Closure
{
    return function (mixed $item): mixed {
        // $item now has an ID (it's been saved)
        $value = $this->getRequestValue();

        // Save related records
        $item->tags()->sync($value);

        return $item;
    };
}
```

**When to use each:**
- `resolveOnBeforeApply()` - Validation, preparing data
- `resolveOnApply()` - Setting field value on model
- `resolveOnAfterApply()` - Relationships, file uploads, side effects

**Execution order:**
```
1. resolveOnBeforeApply()
2. resolveOnApply()
3. Model->save()
4. resolveOnAfterApply()
```

#### 7. `prepareBeforeRender()` - Prepare Logic Before Rendering

**Purpose:** Process data, modify attributes, prepare field state BEFORE rendering the view. This is where business logic should live, NOT in Blade views!

**IMPORTANT:** Keep Blade views clean - move ALL logic to `prepareBeforeRender()`.

```php
protected function prepareBeforeRender(): void
{
    parent::prepareBeforeRender();

    // Example: Add custom attributes based on field state
    if ($this->isReadOnly) {
        $this->customAttributes([
            'readonly' => true,
            'disabled' => true,
        ]);
    }

    // Example: Merge styles
    if ($this->width && $this->height) {
        $style = "width: {$this->width}px; height: {$this->height}px;";
        $this->customAttributes([
            'style' => $style,
        ]);
    }

    // Example: Add Alpine.js initialization
    if ($this->hasAutocomplete) {
        $this->customAttributes([
            'x-data' => 'autocomplete',
            'x-init' => 'init()',
        ]);
    }

    // Example: Remove 'name' attribute for virtual fields
    if ($this->isVirtual) {
        $this->removeAttribute('name');
    }
}
```

**Common use cases:**
- Preparing attributes for the view
- Merging styles or classes dynamically
- Adding or removing HTML attributes
- Adding Alpine.js directives
- Conditional logic based on field state
- Preparing data for rendering

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
    if ($lazy) {
        $imageAttributes['loading'] = 'lazy';
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

    if ($this->lazy) {
        $this->imageAttributes['loading'] = 'lazy';
    }
}

protected function viewData(): array
{
    return [
        'imageAttributes' => $this->imageAttributes,
    ];
}
```

**Real example - Virtual field:**
```php
protected function prepareBeforeRender(): void
{
    parent::prepareBeforeRender();

    // Remove 'name' attribute so field isn't submitted
    $this->removeAttribute('name');
}

public function isCanApply(): bool
{
    return false; // Virtual fields don't save
}
```

---

<a name="view-templates"></a>
## View Templates

### Basic Structure

```blade
@props([
    'value' => '',
    'isHighlighted' => false,
    'variant' => 'default',
])
<div {{ $attributes->merge(['class' => 'custom-field']) }}>
    <input
        type="text"
        value="{{ $value }}"
        @if($isHighlighted) class="highlighted" @endif
    />
</div>
```

### System Data (Always Available)

**IMPORTANT:** The base `Field` class automatically provides these to ALL views:

```blade
@props([
    'value',        // ← Always available (from systemViewData)
    'attributes',   // ← Always available (HTML attributes bag)
    'label',        // ← Always available
    'column',       // ← Always available
    'errors',       // ← Always available
])

<!-- You can use these immediately -->
<div>
    <label>{{ $label }}</label>
    <input
        type="text"
        value="{{ $value }}"
        {{ $attributes }}
    />
    @if($errors)
        <span class="error">{{ $errors }}</span>
    @endif
</div>
```

### Using `$attributes` Bag

The `$attributes` bag contains all HTML attributes passed to the field:

```blade
<div {{ $attributes }}>
    <!-- $attributes includes: name, id, class, data-*, etc. -->
</div>

<!-- Merge with custom classes -->
<input {{ $attributes->merge(['class' => 'my-custom-class']) }} />

<!-- Except certain attributes -->
<div {{ $attributes->except(['name', 'id']) }}>
```

### Accessing Additional Field Data

Everything from `viewData()` PLUS system data is available in `@props`:

```php
// In PHP class
protected function viewData(): array
{
    return [
        // Don't pass 'value' - it's already available!
        'options' => $this->getOptions(),
        'isReadonly' => $this->isReadonly(),
    ];
}
```

```blade
<!-- In Blade template -->
@props([
    // System data (always available, don't need to declare)
    'value',       // ← From systemViewData()
    'attributes',  // ← From systemViewData()
    'label',       // ← From systemViewData()

    // Your custom data from viewData()
    'options',
    'isReadonly',
])

<select {{ $attributes }} @if($isReadonly) disabled @endif>
    @foreach($options as $key => $label)
        <option value="{{ $key }}" @selected($key == $value)>
            {{ $label }}
        </option>
    @endforeach
</select>
```

### Using MoonShine Components

You can use MoonShine components inside your field view:

```blade
@props(['value' => ''])

<div {{ $attributes }}>
    <x-moonshine::form.input
        type="text"
        :value="$value"
        ::attributes="$attributes"
    />
</div>
```

### Alpine.js Integration

MoonShine includes Alpine.js. You can use it for interactivity:

```blade
@props(['value' => ''])

<div x-data="{ count: {{ $value ?? 0 }} }">
    <button @click="count++">Increment</button>
    <span x-text="count"></span>

    <input
        type="hidden"
        {{ $attributes }}
        x-model="count"
    />
</div>
```

---

<a name="key-methods-reference"></a>
## Key Methods Reference

### Value Access Methods

**CRITICAL:** Always use the correct method for the context!

```php
// Get raw value from model (use in viewData() for default mode)
$this->toValue()

// Get formatted value (use in resolvePreview() for preview mode)
$this->toFormattedValue()

// Get value from request (form submission)
$this->getRequestValue()

// Get field's database column name
$this->getColumn()

// Get field's label
$this->getLabel()
```

**Best practices for value methods:**

```php
// ✅ CORRECT - Use toValue() in viewData() for form inputs
protected function viewData(): array
{
    return [
        'value' => $this->toValue(), // Raw value for input
        'options' => $this->options,
    ];
}

// ✅ CORRECT - Use toFormattedValue() in resolvePreview() for display
protected function resolvePreview(): Renderable|string
{
    $value = $this->toFormattedValue(); // Formatted value for display
    return (string) $value;
}

// ❌ WRONG - Don't use resolveValue() to get value
protected function viewData(): array
{
    return [
        'value' => $this->resolveValue(), // Wrong! This calls render logic
    ];
}
```

**Why this matters:**
- `toValue()` - Returns the raw value from the model, perfect for form inputs
- `toFormattedValue()` - Applies formatters (from `make()` 3rd parameter), perfect for display
- `resolveValue()` - Contains rendering logic, should NOT be called manually

### Attribute Methods

```php
// Get HTML attributes
$this->getAttributes()

// Get specific attribute
$this->getAttribute('placeholder')

// Remove attribute
$this->removeAttribute('name')

// Add custom wrapper attributes
$this->customWrapperAttributes(['class' => 'my-wrapper'])
```

### State Methods

```php
// Check if field is in preview mode
$this->isPreviewMode()

// Check if field is in default mode (form input)
$this->isDefaultMode()

// Check if field is in raw mode
$this->isRawMode()

// Check if field can be applied (saved)
$this->isCanApply()
```

---

<a name="fluent-methods"></a>
## Fluent Methods

Fluent methods allow users to configure your field with method chaining:

```php
Preview::make('Content')
    ->boolean()
    ->image()
```

### Creating Fluent Methods

```php
class Preview extends Field
{
    protected bool $isBoolean = false;
    protected bool $isImage = false;

    public function boolean(
        mixed $hideTrue = null,
        mixed $hideFalse = null
    ): static {
        $this->isBoolean = true;
        $this->hideTrue = $hideTrue ?? false;
        $this->hideFalse = $hideFalse ?? false;

        return $this; // ← Must return $this for chaining
    }

    public function image(): static
    {
        $this->isImage = true;

        return $this; // ← Must return $this
    }
}
```

**Usage:**
```php
Preview::make('Status')->boolean()
Preview::make('Avatar')->image()
Preview::make('Active')->boolean(hideTrue: true)
```

### Common Fluent Patterns

```php
// Toggle feature
public function withSomething(): static
{
    $this->hasSomething = true;
    return $this;
}

// Configure option
public function variant(string $variant): static
{
    $this->variant = $variant;
    return $this;
}

// Set related data
public function options(array $options): static
{
    $this->options = $options;
    return $this;
}
```

---

<a name="field-modes"></a>
## Field Modes

Fields have three rendering modes:

### 1. Default Mode (Forms)

Interactive input for data entry. Used in FormBuilder.

```php
Text::make('Title') // Renders <input type="text">
```

### 2. Preview Mode (Tables)

Read-only display. Used in TableBuilder.

```php
Text::make('Title') // Renders formatted text value
```

The field automatically switches to preview mode when used in TableBuilder. You control the display via `resolvePreview()`:

```php
protected function resolvePreview(): Renderable|string
{
    return "<strong>{$this->toFormattedValue()}</strong>";
}
```

### 3. Raw Mode (Export)

Unformatted value for exports.

```php
Date::make('Created') // Returns raw timestamp
```

---

<a name="relationship-fields"></a>
## Relationship Fields

For fields that work with relationships (HasMany, BelongsToMany), use `resolveOnAfterApply()` instead of `resolveOnApply()`.

**Why?** Because during `resolveOnApply()`, the parent model might not have an ID yet (it hasn't been saved).

```php
class Tags extends Field
{
    // Don't use resolveOnApply for relationships!
    protected function resolveOnApply(): ?Closure
    {
        return static fn($item) => $item;
    }

    // Use resolveOnAfterApply instead
    protected function resolveOnAfterApply(): ?Closure
    {
        return function (mixed $item): mixed {
            // $item has been saved and has an ID
            $tags = $this->getRequestValue();

            // Now we can sync the relationship
            $item->tags()->sync($tags);

            return $item;
        };
    }
}
```

---

<a name="assets"></a>
## Assets (CSS/JS)

### Adding Assets to Field

**IMPORTANT:** The `assets()` method MUST be `protected`, not `public`.

```php
use MoonShine\AssetManager\Css;
use MoonShine\AssetManager\Js;

class Quill extends Textarea
{
    protected string $view = 'admin.fields.quill';

    protected function assets(): array
    {
        return [
            Css::make('/css/quill.snow.css'),
            Js::make('/js/quill.js'),
            Js::make('/js/quill-init.js'),
        ];
    }
}
```

### Asset Locations

Store assets in `public/` directory:
```
public/
├── css/
│   └── quill.snow.css
└── js/
    ├── quill.js
    └── quill-init.js
```

### Alpine.js Component

```js
document.addEventListener('alpine:init', () => {
    Alpine.data('quill', () => ({
        editor: null,

        init() {
            this.editor = new Quill(this.$refs.editor, {
                theme: 'snow'
            });

            this.editor.on('text-change', () => {
                this.$refs.input.value = this.editor.root.innerHTML;
            });
        },
    }))
})
```

**Usage in Blade:**

**IMPORTANT:** Always add `{{ $attributes }}` to the root element!

```blade
<div x-data="quill" {{ $attributes->except(['name']) }}>
    <div x-ref="editor"></div>
    <input type="hidden" name="{{ $attributes->get('name') }}" x-ref="input" />
</div>
```

**Why this matters:**
- Allows configuring field attributes from PHP (class, id, data-*, etc.)
- Ensures proper integration with MoonShine's form system
- `name` attribute goes on the actual input, not the wrapper

### Multiple Fields on One Page

**CRITICAL:** When multiple fields of the same type can exist on one page, ensure unique IDs and avoid script initialization conflicts.

**Problem example:**
```blade
<!-- ❌ WRONG - Hardcoded ID, will conflict! -->
<div x-data="yandexMap" id="map">
    <!-- Multiple fields will have same ID! -->
</div>
```

**Correct approach:**
```blade
<!-- ✅ CORRECT - Unique ID for each field -->
<div
    x-data="yandexMap({
        lat: {{ $value['lat'] ?? $defaultLat }},
        lng: {{ $value['lng'] ?? $defaultLng }},
        fieldId: '{{ $attributes->get('id', 'field-' . uniqid()) }}'
    })"
    {{ $attributes->except(['name']) }}
    x-init="initMap()"
>
    <div :id="fieldId" class="map-container"></div>
    <input type="hidden" {{ $attributes->only(['name']) }} x-model="coordinates" />
</div>
```

**Alpine.js component with unique instances:**
```js
document.addEventListener('alpine:init', () => {
    Alpine.data('yandexMap', (config) => ({
        fieldId: config.fieldId,
        lat: config.lat,
        lng: config.lng,
        map: null,

        initMap() {
            // Use unique fieldId for this instance
            this.map = new ymaps.Map(this.fieldId, {
                center: [this.lat, this.lng],
                zoom: config.zoom
            });

            // Each field instance has its own map
            this.map.events.add('click', (e) => {
                const coords = e.get('coords');
                this.lat = coords[0];
                this.lng = coords[1];
            });
        }
    }));
});
```

**Key principles:**
1. **Generate unique IDs** - Use `uniqid()` or field's actual `id` attribute
2. **Pass config to Alpine component** - Don't use global state
3. **Initialize per-instance** - Each field manages its own state
4. **Use `{{ $attributes }}`** - Allow customization from PHP

**Real-world example:**
```blade
@props([
    'value',
    'attributes',
    'defaultLat' => 55.751244,
    'defaultLng' => 37.618423,
    'defaultZoom' => 10,
])

<div
    x-data="yandexMap({
        lat: {{ $value['lat'] ?? $defaultLat }},
        lng: {{ $value['lng'] ?? $defaultLng }},
        zoom: {{ $defaultZoom }},
        fieldId: '{{ $attributes->get('id', 'yandex-map-' . uniqid()) }}'
    })"
    x-init="initMap()"
    {{ $attributes->except(['name', 'id']) }}
    class="yandex-map-field"
>
    <div class="grid grid-cols-1 gap-4">
        <div :id="fieldId" class="map-container h-64"></div>

        <input
            type="hidden"
            {{ $attributes->only(['name']) }}
            x-model="coordinates"
        />
    </div>
</div>
```

**This approach ensures:**
- ✅ Multiple fields work independently on the same page
- ✅ No ID conflicts
- ✅ Each field has its own state
- ✅ Attributes can be customized from PHP
- ✅ Scripts initialize correctly for each instance

---

<a name="common-patterns"></a>
## Common Patterns

### Read-Only Field (Preview Only)

```php
class Preview extends Field
{
    protected function resolveValue(): mixed
    {
        // Always show preview, never input
        return $this->preview();
    }

    protected function resolveOnApply(): ?Closure
    {
        // Don't save anything
        return static fn($item) => $item;
    }

    public function isCanApply(): bool
    {
        return false;
    }

    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

        // Remove 'name' attribute so field isn't submitted
        $this->removeAttribute('name');
    }
}
```

### Conditional Display in Preview

```php
use MoonShine\UI\Components\Boolean;

protected function resolvePreview(): Renderable|string
{
    $value = $this->toFormattedValue();

    if ($this->isBoolean) {
        $value = (bool) $value;

        return match (true) {
            $this->hideTrue && $value => '',
            $this->hideFalse && !$value => '',
            default => (string) Boolean::make($value)->render(),
        };
    }

    if ($this->isImage) {
        return Thumbnails::make($value)->render();
    }

    return (string) $value;
}
```

### JSON Field

```php
class JsonEditor extends Field
{
    protected function resolveValue(): mixed
    {
        $value = $this->toValue();
        return is_string($value) ? json_decode($value, true) : $value;
    }

    protected function resolveOnApply(): ?Closure
    {
        return function (mixed $item): mixed {
            $value = $this->getRequestValue();

            // Convert array to JSON string
            $json = is_array($value) ? json_encode($value) : $value;

            data_set($item, $this->getColumn(), $json);

            return $item;
        };
    }

    protected function viewData(): array
    {
        return [
            'value' => json_encode($this->toValue(), JSON_PRETTY_PRINT),
        ];
    }
}
```

### File Upload Field

```php
use Illuminate\Support\Facades\Storage;

class CustomUpload extends Field
{
    protected function resolveOnApply(): ?Closure
    {
        return function (mixed $item): mixed {
            $file = $this->getRequestValue();

            if ($file && $file instanceof \Illuminate\Http\UploadedFile) {
                // Delete old file
                $oldPath = data_get($item, $this->getColumn());
                if ($oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }

                // Store new file
                $path = $file->store('uploads', 'public');
                data_set($item, $this->getColumn(), $path);
            }

            return $item;
        };
    }
}
```

---

<a name="best-practices"></a>
## Best Practices

### 1. Always Return `$this` in Fluent Methods

```php
// ✅ Correct
public function variant(string $variant): static
{
    $this->variant = $variant;
    return $this;
}

// ❌ Wrong
public function variant(string $variant): void
{
    $this->variant = $variant;
}
```

### 2. Always Return Model in `resolveOnApply()`

```php
// ✅ Correct
protected function resolveOnApply(): ?Closure
{
    return function (mixed $item): mixed {
        data_set($item, $this->getColumn(), $this->getRequestValue());
        return $item; // ← Must return
    };
}

// ❌ Wrong
protected function resolveOnApply(): ?Closure
{
    return function (mixed $item): mixed {
        data_set($item, $this->getColumn(), $this->getRequestValue());
        // Forgot to return!
    };
}
```

### 3. Use Relationships in `resolveOnAfterApply()`

```php
// ✅ Correct - for relationships
protected function resolveOnAfterApply(): ?Closure
{
    return function (mixed $item): mixed {
        $item->tags()->sync($this->getRequestValue());
        return $item;
    };
}

// ❌ Wrong - model might not have ID yet
protected function resolveOnApply(): ?Closure
{
    return function (mixed $item): mixed {
        $item->tags()->sync($this->getRequestValue()); // Error!
        return $item;
    };
}
```

### 4. Pass Only ADDITIONAL Data to View

```php
// ✅ Correct - only pass custom data
protected function viewData(): array
{
    return [
        // Don't pass 'value', 'attributes', 'label' - they're automatic!
        'options' => $this->options,
        'isDisabled' => $this->isDisabled,
        'maxStars' => $this->maxStars,
    ];
}

// ❌ Wrong - passing system data that's already available
protected function viewData(): array
{
    return [
        'value' => $this->toValue(), // ← Redundant!
        'attributes' => $this->getAttributes(), // ← Redundant!
        'options' => $this->options,
    ];
}

// ✅ Correct - empty if no additional data needed
protected function viewData(): array
{
    return []; // OK if you only use system data
}
```

### 5. Move Logic to `prepareBeforeRender()`

```php
// ❌ BAD - Logic in Blade view
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

### 6. Use Type Hints

```php
// ✅ Correct
protected function resolvePreview(): Renderable|string
{
    return (string) $this->toFormattedValue();
}

public function variant(string $variant): static
{
    $this->variant = $variant;
    return $this;
}

// ❌ Wrong - no type hints
protected function resolvePreview()
{
    return $this->toFormattedValue();
}

public function variant($variant)
{
    $this->variant = $variant;
    return $this;
}
```

### 7. Handle Null Values Gracefully

```php
protected function resolvePreview(): Renderable|string
{
    $value = $this->toFormattedValue();

    // Handle null/empty values
    if ($value === null || $value === '') {
        return '<span class="text-muted">—</span>';
    }

    return (string) $value;
}
```

### 8. Always Add `{{ $attributes }}` to Root Element

```blade
<!-- ✅ CORRECT - Attributes on root element -->
<div {{ $attributes->except(['name']) }} class="my-field">
    <input type="hidden" {{ $attributes->only(['name']) }} />
</div>

<!-- ❌ WRONG - No attributes, can't be customized -->
<div class="my-field">
    <input type="hidden" name="field" />
</div>
```

### 9. Handle Multiple Fields on One Page

```blade
<!-- ✅ CORRECT - Unique IDs for each instance -->
<div
    x-data="myField({
        fieldId: '{{ $attributes->get('id', 'field-' . uniqid()) }}'
    })"
    {{ $attributes }}
>
    <div :id="fieldId"></div>
</div>

<!-- ❌ WRONG - Hardcoded ID, conflicts with multiple fields -->
<div x-data="myField" id="my-field">
    <div id="container"></div>
</div>
```

### 10. Use Helper Functions

```php
use function MoonShine\UI\Fields\value;

public function boolean(mixed $hideTrue = null): static
{
    // value() helper evaluates closures and callables
    $this->hideTrue = value($hideTrue, $this) ?? false;

    return $this;
}
```

---

## Field Stub Template

When you run `php artisan moonshine:field`, this is the stub used:

```php
<?php

declare(strict_types=1);

namespace {namespace};

use {extend};
use Closure;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use Illuminate\Contracts\Support\Renderable;

class DummyClass extends {extendShort}
{
    protected string $view = '{view}';

    protected function reformatFilledValue(mixed $data): mixed
    {
        return parent::reformatFilledValue($data);
    }

    protected function prepareFill(array $raw = [], ?DataWrapperContract $casted = null, int $index = 0): mixed
    {
        return parent::prepareFill($raw, $casted, $index);
    }

    protected function resolveValue(): mixed
    {
        return $this->toValue();
    }

    protected function resolvePreview(): Renderable|string
    {
        return (string) ($this->toFormattedValue() ?? '');
    }

    protected function resolveOnApply(): ?Closure
    {
        return function (mixed $item): mixed {
            return data_set($item, $this->getColumn(), $this->getRequestValue());
        };
    }

    protected function viewData(): array
    {
        return [
            //
        ];
    }
}
```

**Placeholders:**
- `{namespace}` - Field namespace (e.g., `App\MoonShine\Fields`)
- `{extend}` - Full parent class name (e.g., `MoonShine\UI\Fields\Text`)
- `{extendShort}` - Short parent class name (e.g., `Text`)
- `{view}` - View path (e.g., `admin.fields.your-field`)
- `DummyClass` - Your field name

---

## Complete Example: RatingField

A full example of a custom rating field with stars:

**PHP Class:**
```php
<?php

declare(strict_types=1);

namespace App\MoonShine\Fields;

use Closure;
use MoonShine\UI\Fields\Field;
use Illuminate\Contracts\Support\Renderable;

class Rating extends Field
{
    protected string $view = 'admin.fields.rating';

    protected int $maxStars = 5;
    protected string $variant = 'stars';

    public function max(int $max): static
    {
        $this->maxStars = $max;
        return $this;
    }

    public function variant(string $variant): static
    {
        $this->variant = $variant;
        return $this;
    }

    protected function resolveValue(): mixed
    {
        return (int) $this->toValue();
    }

    protected function resolvePreview(): Renderable|string
    {
        $value = (int) $this->toFormattedValue();
        $stars = str_repeat('⭐', $value);

        return "<span class='rating'>{$stars}</span>";
    }

    protected function resolveOnApply(): ?Closure
    {
        return function (mixed $item): mixed {
            $value = (int) $this->getRequestValue();

            // Validate range
            if ($value < 0 || $value > $this->maxStars) {
                $value = 0;
            }

            data_set($item, $this->getColumn(), $value);

            return $item;
        };
    }

    protected function viewData(): array
    {
        return [
            // 'value' is already available from systemViewData()
            'maxStars' => $this->maxStars,
            'variant' => $this->variant,
        ];
    }
}
```

**Blade View:**
```blade
@props([
    'value' => 0,
    'maxStars' => 5,
    'variant' => 'stars',
])

<div x-data="{ rating: {{ $value }} }" class="rating-field">
    <div class="stars">
        @for($i = 1; $i <= $maxStars; $i++)
            <span
                @click="rating = {{ $i }}"
                class="star"
                :class="{ 'active': rating >= {{ $i }} }"
            >
                @if($variant === 'stars')
                    ⭐
                @else
                    {{ $i }}
                @endif
            </span>
        @endfor
    </div>

    <input
        type="hidden"
        {{ $attributes }}
        x-model="rating"
    />
</div>

<style>
.rating-field .star {
    cursor: pointer;
    opacity: 0.3;
    transition: opacity 0.2s;
}
.rating-field .star.active {
    opacity: 1;
}
</style>
```

**Usage:**
```php
Rating::make('Score')
    ->max(10)
    ->variant('numbers')
```

---

## Summary

**Key Takeaways:**

1. Fields have **two parts**: PHP class + Blade view
2. Use `php artisan moonshine:field` to generate scaffolding
3. **Critical methods**:
   - `viewData()` - Pass data to view
   - `resolveValue()` - Get value for form input
   - `resolvePreview()` - Display in tables
   - `resolveOnApply()` - Save to model
   - `resolveOnAfterApply()` - For relationships
4. **Fluent methods** must return `static`
5. **Always return `$item`** in apply closures
6. Use `@props` in Blade to receive data from `viewData()`
7. Files go in:
   - `app/MoonShine/Fields/` (PHP)
   - `resources/views/admin/fields/` (Blade)
