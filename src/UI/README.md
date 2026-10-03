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

Each `unescape…()` method has an `escape…(bool $escape = true)` counterpart. An explicit field/component setting takes precedence over the global setting in either direction. Label methods are also available on components with labels, for example `ActionButton::make('<b>Open</b>')->unescapeLabel()`.

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

These settings control display text. `getLabel()` and `getHint()` still return the original text, and existing value methods `escape()`, `unescape()` and `escapeOnApply()` keep their separate purpose. Renderable views and generated component markup are rendered as HTML. When upgrading HTML supplied as a plain string, enable the appropriate `unescape…()` method explicitly.

Relation `modalMode()` buttons inherit the field's label preference; `modifyButton` can override it. Preview link name callbacks still receive the prepared preview value, and its escaped entities are preserved when escaping the callback result. Popover editing uses the field's value `escape()` / `unescape()` preference independently of global or local label settings.

Table column-selection toggles inherit each field's label preference. Handler buttons inherit the handler's label preference before `modifyButton` runs, so that callback can override it.

Display escaping is prepared in PHP. Anonymous Blade views delegate text rendering to the internal `DisplayText` component, whose `viewData()` resolves the preference and prepares the content. Card value labels and tab labels are prepared alongside their raw data in `viewData()`. Templates do not read escaping configuration or select escaping policies. Raw labels and value keys remain available for serialization and attributes.

Class-backed Blade components accept a local preference through `:escape-label`, for example `<x-moonshine::action-button :label="$label" :escape-label="false" />`. The constructor receives this preference before view data is prepared. A direct Blade `Link` displays its label when its slot is empty; an explicit slot takes precedence. Card labels also respect `values`, `escapeLabel`, and `escapeValueLabels` supplied through `customView()`.
