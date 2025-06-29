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
     * @param list<T> $items
     */
    public function __construct(private readonly string $type, private array $items)
    {
    }

    private function getType(): string
    {
        return $this->type;
    }
    /**
     * @return list<T>
     */
    private function getItems(): array
    {
        /** @var Collection<array-key, T> $collection */
        $collection = new Collection($this->items);

        return $collection
            ->ensure($this->getType())
            ->toArray();
    }

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
        $condition = static fn (object $item): bool => Collection::make($data)->every(
            fn (object|string $i): bool => match (true) {
                \is_string($i) => $item::class !== $i,
                \is_callable($i) => ! $i($item),
                default => $i !== $item,
            }
        );

        $this->items = Collection::make($this->items)
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
        $condition = static fn (object $item): bool => Collection::make($data)->contains(
            fn (object|string $i): bool => match (true) {
                \is_string($i) => $item::class === $i,
                \is_callable($i) => $i($item),
                default => $i === $item,
            }
        );

        $this->items = Collection::make($this->items)
        ->filter($condition)
        ->toArray();

        return $this;
    }

    /**
     * @return ListOf<T>
     */
    public function add(object ...$data): self
    {
        $this->items = Collection::make($this->items)
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
            $this->items = Collection::make($this->items)
                ->prepend($item)
                ->toArray();
        }

        return $this;
    }

    /**
     * @return list<T>
     */
    public function toArray(): array
    {
        return $this->getItems();
    }
}
