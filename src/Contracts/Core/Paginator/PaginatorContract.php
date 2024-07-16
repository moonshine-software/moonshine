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

    public function getLinks(): PaginatorLinksContract;

    public function getData(): Collection;

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

    public function getTranslates(): array;
}
