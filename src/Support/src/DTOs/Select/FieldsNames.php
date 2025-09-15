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
final class FieldsNames implements Arrayable
{
    use Makeable;

    /**
     * @var array<string, mixed>
     */
    protected array $values = [
        'valueField' => null,         // default: value
        'labelField' => null,         // default: label
        'descriptionField' => null,   // default: description

        'childrenField' => null,      // default: values
        'optgroupValueField' => null, // default: value
        'optgroupLabelField' => null, // default: label
        'optgroupField' => null,      // default: optgroup

        'searchField' => null,        // default: ['label']
        'disabledField' => null,      // default: disabled
        'sortField' => null,           // default: $order
    ];

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(array $values = [])
    {
        $this->fromArray($values);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        if (\is_null($this->values['searchField']) && ! \is_null($this->values['labelField'])) {
            $this->search([$this->values['labelField']]);
        }

        return $this->values;
    }

    /**
     * @param array<string, mixed> $values
     */
    public function fromArray(array $values): self
    {
        foreach ($values as $name => $value) {
            if (method_exists($this, $name)) {
                $this->$name($value);
            }
        }

        return $this;
    }

    private function set(string $name, mixed $value): self
    {
        $this->values[$name] = $value;

        return $this;
    }

    public function value(string $value): self
    {
        return $this->set('valueField', $value);
    }

    public function label(string $value): self
    {
        return $this->set('labelField', $value);
    }

    public function description(string $value): self
    {
        return $this->set('descriptionField', $value);
    }

    public function children(string $value): self
    {
        return $this->set('childrenField', $value);
    }

    public function optgroupValue(string $value): self
    {
        return $this->set('optgroupValueField', $value);
    }

    public function optgroupLabel(string $value): self
    {
        return $this->set('optgroupLabelField', $value);
    }

    public function optgroup(string $value): self
    {
        return $this->set('optgroupField', $value);
    }

    /**
     * @param array<mixed> $value
     */
    public function search(array $value): self
    {
        return $this->set('searchField', $value);
    }

    public function disabled(string $value): self
    {
        return $this->set('disabledField', $value);
    }

    public function sort(string $value): self
    {
        return $this->set('sortField', $value);
    }
}
