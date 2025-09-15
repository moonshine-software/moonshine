<?php

declare(strict_types=1);

namespace MoonShine\Support\DTOs\Select;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use MoonShine\Support\Traits\Makeable;

/**
 * @method static static make(array $values = [])
 *
 * @implements Arrayable<string, mixed>
 */
final class AsyncSettings implements Arrayable
{
    use Makeable;

    /**
     * @var array<string, mixed>
     */
    protected array $values = [
        'queryKey' => null, // default: query
        'selectedValuesKey' => null,
        'resultKey' => null,
        'withAllFields' => false,
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
                $this->$name($value);
            }
        }

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->values as $name => $value) {
            if (\is_null($value)) {
                continue;
            }

            $result['data-async-' . Str::snake($name, '-')] = $value;
        }

        return $result;
    }

    private function set(string $name, mixed $value): self
    {
        $this->values[$name] = $value;

        return $this;
    }

    public function queryKey(string $value): self
    {
        return $this->set('queryKey', $value);
    }

    public function selectedValuesKey(string $value): self
    {
        return $this->set('selectedValuesKey', $value);
    }

    public function resultKey(string $value): self
    {
        return $this->set('resultKey', $value);
    }

    public function withAllFields(bool $value = true): self
    {
        return $this->set('withAllFields', $value);
    }
}
