<?php

declare(strict_types=1);

namespace MoonShine\Support;

/**
 * @template-covariant T
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
        return collect($this->items)
            ->ensure($this->getType())
            ->toArray();
    }

    /**
     * @param  object|class-string<T>  ...$data
     *
     * @return ListOf<T>
     */
    public function except(object|string ...$data): self
    {
        $condition = static function(object $item) use($data): bool {
            return collect($data)->every(
                fn(object|string $i) => match(true) {
                    is_string($i) => get_class($item) !== $i,
                    is_callable($i) => !$i($item),
                    default => $i !== $item,
                }
            );
        };

        $this->items = collect($this->items)
            ->filter($condition)
            ->toArray();

        return $this;
    }

    /**
     * @param  object ...$data
     *
     * @return ListOf<T>
     */
    public function add(object ...$data): self
    {
        $this->items = collect($this->items)
            ->push(...$data)
            ->toArray();

        return $this;
    }

    /**
     * @param  object ...$data
     *
     * @return ListOf<T>
     */
    public function prepend(object ...$data): self
    {
        foreach ($data as $item) {
            $this->items = collect($this->items)
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
