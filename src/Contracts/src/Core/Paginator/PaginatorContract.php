<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\Paginator;

use ArrayAccess;
use Countable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use IteratorAggregate;
use JsonSerializable;
use Stringable;

/**
 * @template TData of mixed = mixed
 *
 * @extends Arrayable<array-key, TData>
 * @extends ArrayAccess<array-key, TData>
 * @extends IteratorAggregate<array-key, TData>
 */
interface PaginatorContract extends
    Arrayable,
    JsonSerializable,
    ArrayAccess,
    Countable,
    IteratorAggregate,
    Stringable
{
    public function getPath(): string;

    public function setPath(string $path): static;

    public function getPageName(): string;

    public function setPageName(string $name): static;

    public function getLinks(): PaginatorLinksContract;

    /**
     * @return Collection<array-key, TData>
     */
    public function getData(): Collection;

    /**
     * @return Collection<array-key, TData>
     */
    public function getOriginalData(): Collection;

    public function isSimple(): bool;

    public function getCurrentPage(): int;

    public function getFrom(): ?int;

    public function getTo(): ?int;

    public function getPerPage(): int;

    public function getTotal(): ?int;

    public function getLastPage(): ?int;

    public function getFirstPageUrl(): ?string;

    public function getNextPageUrl(): ?string;

    public function getPrevPageUrl(): ?string;

    public function getLastPageUrl(): ?string;

    public function async(): static;

    public function isAsync(): bool;

    /**
     * @return array<string, mixed>
     */
    public function getTranslates(): array;
}
