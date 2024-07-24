<?php

declare(strict_types=1);

namespace MoonShine\Core\Paginator;

use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\Paginator\PaginatorContract;
use MoonShine\Contracts\Core\Paginator\PaginatorLinksContract;
use MoonShine\Core\Traits\WithCore;
use MoonShine\Core\Traits\WithViewRenderer;
use Traversable;

final class Paginator implements PaginatorContract
{
    use WithCore;
    use WithViewRenderer;

    private bool $async = false;

    public function __construct(
        private string $path,
        private iterable $links,
        private readonly iterable $data,
        private readonly iterable $originalData,
        private readonly int $currentPage,
        private readonly ?int $from,
        private readonly ?int $to,
        private readonly int $perPage,
        private readonly bool $simple = false,
        private readonly ?int $total = null,
        private readonly ?int $lastPage = null,
        private readonly ?string $firstPageUrl = null,
        private readonly ?string $prevPageUrl = null,
        private readonly ?string $lastPageUrl = null,
        private readonly ?string $nextPageUrl = null,
        array $translates = [],
    ) {
        $this->translates = $translates;
    }

    public function getLinks(): PaginatorLinksContract
    {
        return PaginatorLinks::make($this->links)->reject(static fn (array $link): bool => $link['url'] === '' || str($link['label'])->contains(['prev', 'next'], true));
    }

    public function getData(): Collection
    {
        return collect($this->data);
    }

    public function getOriginalData(): Collection
    {
        return collect($this->originalData);
    }

    private function changeLinkUrls(string $path): void
    {
        if($this->path !== $path) {
            $this->links = collect($this->links)
                ->map(function (array $link) use ($path): array {
                    $current = strtok($this->path, '?');
                    $new = strtok($path, '?');
                    $query = (string) str($path)->after('?');

                    $link['url'] = $link['url']
                        ? trim(str_replace($current, $new, $link['url']) . '&' . $query, '&')
                        : $link['url'];

                    return $link;
                })
                ->toArray();
        }
    }

    public function setPath(string $path): static
    {
        $this->changeLinkUrls($path);

        $this->path = $path;

        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getLastPage(): ?int
    {
        return $this->lastPage;
    }

    public function getFrom(): ?int
    {
        return $this->from;
    }

    public function getTo(): ?int
    {
        return $this->to;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getFirstPageUrl(): ?string
    {
        return $this->firstPageUrl;
    }

    public function getNextPageUrl(): ?string
    {
        return $this->nextPageUrl;
    }

    public function getPrevPageUrl(): ?string
    {
        return $this->prevPageUrl;
    }

    public function getLastPageUrl(): ?string
    {
        return $this->lastPageUrl;
    }

    public function isSimple(): bool
    {
        return $this->simple;
    }

    public function async(): static
    {
        $this->async = true;

        return $this;
    }

    public function isAsync(): bool
    {
        return $this->async;
    }

    public function hasPages(): bool
    {
        return $this->getCurrentPage() !== 1 || ($this->getCurrentPage() < $this->getLastPage());
    }

    public function getView(): string
    {
        return 'moonshine::components.pagination';
    }

    public function systemViewData(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'links' => $this->getLinks()->toArray(),
            'data' => $this->getData()->toArray(),
            'async' => $this->isAsync(),
            'simple' => $this->isSimple(),
            'path' => $this->getPath(),
            'to' => $this->getTo(),
            'from' => $this->getFrom(),
            'total' => $this->getTotal(),
            'per_page' => $this->getPerPage(),
            'current_page' => $this->getCurrentPage(),
            'last_page' => $this->getLastPage(),
            'last_page_url' => $this->getLastPageUrl(),
            'first_page_url' => $this->getFirstPageUrl(),
            'prev_page_url' => $this->getPrevPageUrl(),
            'next_page_url' => $this->getNextPageUrl(),
            'has_pages' => $this->hasPages(),
            'translates' => $this->getTranslates(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function getIterator(): Traversable
    {
        return $this->getData()->getIterator();
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->getData()->has($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->getData()->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->getData()->put($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->getData()->forget($offset);
    }

    public function count(): int
    {
        return $this->getData()->count();
    }
}
