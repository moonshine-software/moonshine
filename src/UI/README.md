# [READ ONLY] Subtree split of the MoonShine UI component

This repository is a readonly MoonShine monorepo subsplit.
Please, open pull requests and issues in the [main repository](https://github.com/moonshine-software/moonshine).

## Display escaping

Labels, hints, prefixes, suffixes and string content returned by `beforeRender()` / `afterRender()` are escaped by default. This also applies to field labels rendered in tables, cards and relation headings, and to component labels such as buttons, links and tabs.

Allow HTML for an individual part explicitly:

```php
Text::make('<strong>Name</strong>', 'name')
    ->unescapeLabel()
    ->hint('<a href="/help">Help</a>')
    ->unescapeHint()
    ->prefix('<span>Prefix</span>')
    ->unescapePrefix()
    ->suffix('<span>Suffix</span>')
    ->unescapeSuffix()
    ->beforeRender(fn () => '<aside>Before</aside>')
    ->unescapeBeforeRender()
    ->afterRender(fn () => '<aside>After</aside>')
    ->unescapeAfterRender();
```

Each `unescape…()` method has an `escape…(bool $escape = true)` counterpart. An explicit field/component setting takes precedence over the global setting in either direction. Label methods are also available on components with labels, for example `ActionButton::make('<b>Open</b>')->unescapeLabel()`. `Footer` applies its label preference to menu labels, and `Tabs` applies it to tabs created from `items`.

Text derived from field values has its own preference, so allowing HTML in a field label never exposes record data:

```php
Text::make('<b>Site</b>', 'site')
    ->unescapeLabel()                              // field label only
    ->link(fn ($value) => $value, name: fn ($value) => $value)
    ->unescapeLinkName();                          // link names, opt in separately

BelongsToMany::make('Categories', 'categories', resource: CategoryResource::class)
    ->tree('parent_id')
    ->unescapeOptionLabels();                      // tree, horizontal and inline link labels
```

`Heading` labels, explicit `Modal` / `OffCanvas` titles, field link names and related item labels remain escaped even when global label escaping is disabled, because they commonly contain record data. Use their local `unescapeLabel()`, `unescapeTitle()`, `unescapeLinkName()` or `unescapeOptionLabels()` only for trusted HTML. A title derived from an action button label follows the button's label preference; relation headings and modal titles follow the relation field's label preference. Confirmation content and explicit confirmation titles keep escaping enabled by default.

`Htmlable` values, such as `HtmlString`, are treated as prepared HTML. Return one from `beforeRender()` / `afterRender()` to output trusted markup without disabling escaping for the field.

In Laravel, configure the defaults in `config/moonshine.php`:

```php
'escapes' => [
    'label' => true,
    'hint' => true,
    'prefix' => true,
    'suffix' => true,
    'before_render' => true,
    'after_render' => true,
],
```

The corresponding `MoonShineConfigurator` methods are `escapeLabel()`, `escapeHint()`, `escapePrefix()`, `escapeSuffix()`, `escapeBeforeRender()` and `escapeAfterRender()`. Pass `false` to disable a default.

The global label setting applies to developer-provided labels without a local preference, including buttons, fields, boxes, dividers, menus and card value labels. Disabling it allows HTML in those labels; escape any record data included in them explicitly. The record-text contexts listed above keep their own escaping defaults. Prefer local `unescape…()` methods when only specific labels contain HTML.

These settings control display text. `getLabel()` and `getHint()` still return the original text, and existing value methods `escape()`, `unescape()` and `escapeOnApply()` keep their separate purpose. Renderable views and generated component markup are rendered as HTML. When upgrading HTML supplied as a plain string, enable the appropriate `unescape…()` method explicitly.

Relation `modalMode()` buttons inherit the field's label preference; `modifyButton` can override it. Preview link name callbacks still receive the prepared preview value, and its escaped entities are preserved when escaping the callback result. Popover editing uses the field's value `escape()` / `unescape()` preference independently of global or local label settings.

Table column-selection toggles inherit each field's label preference. An explicit handler label preference applies only when its button has no explicit label preference of its own. `modifyButton` runs afterward and can override either preference.

Prefix/suffix rendering uses the field's `isEscapePrefix()` / `isEscapeSuffix()` getters, including overrides in custom fields. The default getters use the explicit field preference, then an explicit extension preference, then the global default. Rendering prepares a clone of the extension, so shared extension instances keep their own preferences. Standalone extensions use the same defaults through `DisplayTextType`.

Display escaping is prepared in PHP. `getLabelHtml()` and `getHintHtml()` return prepared `Htmlable` values, and class-backed components pass them to their views as `labelHtml`, `hintHtml`, `valueHtml` or `titleHtml` next to the raw text. Blade outputs prepared values with `{{ }}` without escaping them again; they serialize to strings. Only raw text passed directly to anonymous Blade views is prepared by the internal `DisplayText` component. Templates do not read escaping configuration or select escaping policies. Raw labels and value keys remain available for serialization and attributes.

Blade slots are template output and are not escaped by default; an explicit `:escape-label="true"` or `:escape-hint="true"` escapes slot content too.

Class-backed Blade components accept a local preference through `:escape-label`, for example `<x-moonshine::action-button :label="$label" :escape-label="false" />`. The constructor receives this preference before view data is prepared. A direct Blade `Link` displays its label when its slot is empty; an explicit slot takes precedence. Card labels also respect `values`, `escapeLabel`, and `escapeValueLabels` supplied through `customView()`.

### Upgrading custom implementations

`HasLabelContract` adds `escapeLabel(bool $escape = true): static`, `unescapeLabel(): static`, `isEscapeLabel(): bool`, `hasLabelEscapingOverride(): bool` and `getLabelHtml(): Htmlable`. Custom implementations must provide these methods; the `WithLabel` trait supplies them. `hasLabelEscapingOverride()` reports whether a local preference was set, independently of its resolved value, so defaults can be inherited without overwriting explicit choices. Keep `getLabel(): string` raw for attributes, serialization and data operations, and use `getLabelHtml()` for display. `HasHintContract` likewise adds `getHintHtml(): Htmlable` alongside its escaping methods; `WithHint` supplies the implementation.
