<?php

declare(strict_types=1);

namespace MoonShine\Support\DTOs\Select;

use Illuminate\Contracts\Support\Arrayable;
use MoonShine\Support\Traits\Makeable;

/**
 * @method static static make(array $values = [])
 *
 * @implements Arrayable<string, mixed>
 */
final class Settings implements Arrayable
{
    use Makeable;

    /**
     * @var array<string, mixed>
     */
    protected array $values = [
        'delimiter' => null,                // default: ',',
        'splitOn' => null,                  // regexp or string for splitting up values from a paste command
        'diacritics' => null,               // default: true
        'highlight' => null,                // default: true
        'openOnFocus' => null,              // default: true
        'shouldOpen' => null,               // default: false
        'maxOptions' => null,               // default: 50
        'maxItems' => null,
        'hideSelected' => null,
        'duplicates' => null,               // default: false
        'selectOnTab' => null,              // default: false
        'allowEmptyOption' => null,         // default: false
        'closeAfterSelect' => null,         // default: false

        'lockOptgroupOrder' => null,        // default: false
        'searchConjunction' => null,        // default: and
        'loadThrottle' => null,             // default: 300
        'refreshThrottle' => null,          // default: 300
        'dropdownParent' => null,
        'controlInput' => null,             // default: <input type="text" autocomplete="off" size="1" />
        'copyClassesToDropdown' => null,    // default: false
        'hidePlaceholder' => null,

        'wrapperClass' => null,             // default: ts-wrapper
        'controlClass' => null,             // default: ts-control
        'dropdownClass' => null,            // default: ts-dropdown
        'dropdownContentClass' => null,     // default: ts-dropdown-content
        'itemClass' => null,                // default: item
        'optionClass' => null,              // default: option
        'loadingClass' => null,             // default: loading
    ];

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(array $values = [])
    {
        $this->fromArray($values);
    }

    /**
     * @param array<string, mixed> $values
     */
    public function fromArray(array $values): self
    {
        foreach ($values as $name => $value) {
            if (\array_key_exists($name, $this->values)) {
                $this->set($name, $value);
            }
        }

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter($this->values, fn ($v): bool => ! \is_null($v));
    }

    public function set(string $name, mixed $value): self
    {
        $this->values[$name] = $value;

        return $this;
    }

    public function delimiter(string $value): self
    {
        return $this->set('delimiter', $value);
    }

    public function splitOn(string $value): self
    {
        return $this->set(
            'splitOn',
            preg_match('/^(.)(.*)\1([a-zA-Z]*)$/s', $value, $matches)
                ? [
                'pattern' => $matches[2],
                'modifiers' => $matches[3],
            ]
                : null
        );
    }

    public function diacritics(bool $value): self
    {
        return $this->set('diacritics', $value);
    }

    public function highlight(bool $value): self
    {
        return $this->set('highlight', $value);
    }

    public function openOnFocus(bool $value): self
    {
        return $this->set('openOnFocus', $value);
    }

    public function shouldOpen(bool $value = true): self
    {
        return $this->set('shouldOpen', $value);
    }

    public function maxOptions(int $value): self
    {
        return $this->set('maxOptions', $value);
    }

    public function maxItems(?int $value): self
    {
        return $this->set('maxItems', $value);
    }

    public function hideSelected(bool $value): self
    {
        return $this->set('hideSelected', $value);
    }

    public function duplicates(bool $value = true): self
    {
        return $this->set('duplicates', $value);
    }

    public function selectOnTab(bool $value = true): self
    {
        return $this->set('selectOnTab', $value);
    }

    public function allowEmptyOption(bool $value = true): self
    {
        return $this->set('allowEmptyOption', $value);
    }

    public function closeAfterSelect(bool $value = true): self
    {
        return $this->set('closeAfterSelect', $value);
    }

    public function lockOptgroupOrder(bool $value = true): self
    {
        return $this->set('lockOptgroupOrder', $value);
    }

    public function searchConjunction(string $value): self
    {
        return $this->set('searchConjunction', $value);
    }

    public function loadThrottle(int $value): self
    {
        return $this->set('loadThrottle', $value);
    }

    public function refreshThrottle(int $value): self
    {
        return $this->set('refreshThrottle', $value);
    }

    public function dropdownParent(string $value): self
    {
        return $this->set('dropdownParent', $value);
    }

    public function copyClassesToDropdown(bool $value = true): self
    {
        return $this->set('copyClassesToDropdown', $value);
    }

    public function wrapperClass(string $value): self
    {
        return $this->set('wrapperClass', $value);
    }

    public function controlClass(string $value): self
    {
        return $this->set('controlClass', $value);
    }

    public function dropdownClass(string $value): self
    {
        return $this->set('dropdownClass', $value);
    }

    public function dropdownContentClass(string $value): self
    {
        return $this->set('dropdownContentClass', $value);
    }

    public function itemClass(string $value): self
    {
        return $this->set('itemClass', $value);
    }

    public function optionClass(string $value): self
    {
        return $this->set('optionClass', $value);
    }

    public function loadingClass(string $value): self
    {
        return $this->set('loadingClass', $value);
    }
}
