<?php

declare(strict_types=1);

namespace MoonShine\Support;

use Illuminate\Support\Collection;

/**
 * @template T
 */
final class ListOf
{
    /**
     * @param  class-string<T>  $type
     * @param array<T> $items
     */
    public function __construct(private readonly string $type, private array $items)
    {
    }

    /**
     * @return class-string<T>
     */
    private function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array<T>
     */
    private function getItems(): array
    {
        /** @var Collection<array-key, T> $collection */
        $collection = new Collection($this->items);

        return $collection
            ->ensure($this->getType())
            ->toArray();
    }

    /**
     * @return self<T>
     */
    public function empty(): self
    {
        return new self($this->getType(), []);
    }

    /**
     * @param  object|class-string<T>  ...$data
     *
     * @return ListOf<T>
     */
    public function except(object|string ...$data): self
    {
        $condition = static fn (object $item): bool => (new Collection($data))->every(
            fn (object|string $i): bool => match (true) {
                \is_string($i) => $item::class !== $i,
                \is_callable($i) => ! $i($item),
                default => $i !== $item,
            }
        );

        /**
         * @var Collection<array-key, T> $collection
         */
        $collection = new Collection($this->items);
        $this->items = $collection
            ->filter($condition)
            ->toArray();

        return $this;
    }

    /**
     * @param  object|class-string<T>  ...$data
     *
     * @return ListOf<T>
     */
    public function only(object|string ...$data): self
    {
        $condition = static fn (object $item): bool => (new Collection($data))->contains(
            fn (object|string|callable $i): bool => match (true) {
                \is_string($i) => $item::class === $i,
                \is_callable($i) => (bool) $i($item),
                default => $i === $item,
            }
        );

        /** @var Collection<array-key, T> $collection */
        $collection = new Collection($this->items);
        $this->items = $collection
            ->filter($condition)
            ->toArray();

        return $this;
    }

    /**
     * @return ListOf<T>
     */
    public function add(object ...$data): self
    {
        /** @var Collection<array-key, T> $collection */
        $collection = new Collection($this->items);
        $this->items = $collection
            ->push(...$data)
            ->toArray();

        return $this;
    }

    /**
     * @return ListOf<T>
     */
    public function prepend(object ...$data): self
    {
        foreach ($data as $item) {
            /** @var Collection<array-key, T> $collection */
            $collection = new Collection($this->items);
            $this->items = $collection
                ->prepend($item)
                ->toArray();
        }

        return $this;
    }

    /**
     * @return array<T>
     */
    public function toArray(): array
    {
        return $this->getItems();
    }
}
