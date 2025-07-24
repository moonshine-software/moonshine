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
final class FieldNames implements Arrayable
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
        'sortField' => null           // default: $order
    ];

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(array $values = []) {
        $this->fromArray($values);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array {
        if (is_null($this->values['searchField']) && ! is_null($this->values['labelField'])) {
            $this->search([$this->values['labelField']]);
        }

        return $this->values;
    }

    /**
     * @param array<string, mixed> $values
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

    public function value(string $value): static {
        return $this->set('valueField', $value);
    }
    public function label(string $value): static {
        return $this->set('labelField', $value);
    }
    public function description(string $value): static {
        return $this->set('descriptionField', $value);
    }

    public function children(string $value): static {
        return $this->set('childrenField', $value);
    }
    public function optgroupValue(string $value): static {
        return $this->set('optgroupValueField', $value);
    }
    public function optgroupLabel(string $value): static {
        return $this->set('optgroupLabelField', $value);
    }
    public function optgroup(string $value): static {
        return $this->set('optgroupField', $value);
    }

    /**
     * @param array<mixed> $value
     */
    public function search(array $value): static {
        return $this->set('searchField', $value);
    }
    public function disabled(string $value): static {
        return $this->set('disabledField', $value);
    }
    public function sort(string $value): static {
        return $this->set('sortField', $value);
    }
}
