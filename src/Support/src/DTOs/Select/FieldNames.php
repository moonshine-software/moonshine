<?php


declare(strict_types=1);

namespace MoonShine\Support\DTOs\Select;

use Illuminate\Contracts\Support\Arrayable;
use MoonShine\Support\Traits\Makeable;

/**
 * @method static static make(array $values = [])
 *
 * @phpstan-type Values array<string, mixed>
 * @implements Arrayable<string, mixed>
 */
final class FieldNames implements Arrayable
{
    use Makeable;

    /**
     * @var Values
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
        'sortField' => null           // default: $order
    ];

    /**
     * @param Values $values
     */
    public function __construct(array $values = []) {
        $this->fromArray($values);
    }

    /**
     * @return Values
     */
    public function toArray(): array {
        if (is_null($this->values['searchField']) && ! is_null($this->values['labelField'])) {
            $this->searchField([$this->values['labelField']]);
        }

        return $this->values;
    }

    /**
     * @param Values $values
     */
    public function fromArray(array $values): static {
        foreach ($values as $name => $value) {
            if (array_key_exists($name, $this->values)) {
                $this->set($name, $value);
            }
        }
        return $this;
    }

    protected function set(string $name, mixed $value): static {
        $this->values[$name] = $value;
        return $this;
    }

    public function valueField(string $value): static {
        return $this->set('valueField', $value);
    }
    public function labelField(string $value): static {
        return $this->set('labelField', $value);
    }
    public function descriptionField(string $value): static {
        return $this->set('descriptionField', $value);
    }

    public function childrenField(string $value): static {
        return $this->set('childrenField', $value);
    }
    public function optgroupValueField(string $value): static {
        return $this->set('optgroupValueField', $value);
    }
    public function optgroupLabelField(string $value): static {
        return $this->set('optgroupLabelField', $value);
    }
    public function optgroupField(string $value): static {
        return $this->set('optgroupField', $value);
    }

    /**
     * @param array<mixed> $value
     */
    public function searchField(array $value): static {
        return $this->set('searchField', $value);
    }
    public function disabledField(string $value): static {
        return $this->set('disabledField', $value);
    }
    public function sortField(string $value): static {
        return $this->set('sortField', $value);
    }
}
