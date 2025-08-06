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
    protected array $dataValues = [
        'queryKey' => null, // default: query
        'selectedValuesKey' => null,
        'resultKey' => null,
        'withAllFields' => false,
    ];

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(array $values = []) {
        $this->fromArray($values);
    }

    /**
     * @param array<string, mixed> $values
     */
    public function fromArray(array $values): self {
        foreach ($values as $name => $value) {
            if (method_exists($this, $name)) {
                $this->$name($value);
            }
        }

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array {
        return [
            ...$this->dataToArray()
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function dataToArray(): array {
        $result = [];
        foreach ($this->dataValues as $name => $value) {
            if (is_null($value)) {
                continue;
            }

            $result['data-async-' . Str::snake($name, '-')] = $value;
        }

        return $result;
    }

    protected function set(string $name, mixed $value, string $type): self {
        if ($type === 'data') {
            $this->dataValues[$name] = $value;
        }

        return $this;
    }

    public function queryKey(string $value): self {
        return $this->set(__FUNCTION__, $value, 'data');
    }

    public function selectedValuesKey(string $value): self {
        return $this->set(__FUNCTION__, $value, 'data');
    }

    public function resultKey(string $value): self {
        return $this->set(__FUNCTION__, $value, 'data');
    }

    public function withAllFields(bool $value = true): self {
        return $this->set(__FUNCTION__, $value, 'data');
    }
}
