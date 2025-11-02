<?php

declare(strict_types=1);

namespace MoonShine\Core\Paginator;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use MoonShine\Contracts\Core\HasCoreContract;
use MoonShine\Contracts\Core\HasViewRendererContract;
use MoonShine\Contracts\Core\Paginator\PaginatorContract;
use MoonShine\Contracts\Core\Paginator\PaginatorLinksContract;
use MoonShine\Core\Traits\WithCore;
use MoonShine\Core\Traits\WithViewRenderer;
use Traversable;

final class Paginator implements PaginatorContract, HasCoreContract, HasViewRendererContract
{
    use WithCore;
    use WithViewRenderer;

    private bool $async = false;

    /**
     * @param  iterable<string, mixed>  $links
     * @param  iterable<array-key, mixed>  $data
     * @param  iterable<array-key, mixed>  $originalData
     * @param  array<string, string>  $translates
     */
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
        private ?string $firstPageUrl = null,
        private ?string $prevPageUrl = null,
        private ?string $lastPageUrl = null,
        private ?string $nextPageUrl = null,
        private string $pageName = 'page',
        array $translates = [],
    ) {
        $this->translates = $translates;
    }

    public function getLinks(): PaginatorLinksContract
    {
        return PaginatorLinks::make($this->links)
            ->reject(static fn (array $link): bool => $link['url'] === '' || ! is_numeric($link['label']));
    }

    /**
     * @return Collection<array-key, mixed>
     */
    public function getData(): Collection
    {
        return new Collection($this->data);
    }

    /**
     * @return Collection<array-key, mixed>
     */
    public function getOriginalData(): Collection
    {
        return new Collection($this->originalData);
    }

    private function changeLinkUrls(string $path): void
    {
        if ($this->path !== $path) {
            $changeUrl = function (?string $link) use ($path): ?string {
                $current = strtok($this->path, '?');
                $new = strtok($path, '?');
                $query = Str::of($path)->contains('?') ? Str::of($path)->after('?')->value() : '';

                return $link
                    ? trim(
                        str_replace(
                            $current === false ? '' : $current,
                            $new === false ? '' : $new,
                            $link
                        ) . '&' . $query,
                        '&'
                    )
                    : $link;
            };

            $this->nextPageUrl = $changeUrl($this->nextPageUrl);
            $this->firstPageUrl = $changeUrl($this->firstPageUrl);
            $this->prevPageUrl = $changeUrl($this->prevPageUrl);
            $this->lastPageUrl = $changeUrl($this->lastPageUrl);

            /** @var Collection<array-key, array<string, mixed>> $linksCollection */
            $linksCollection = new Collection($this->links);

            /**
             * @var array<string, mixed> $links
             */
            $links = $linksCollection->map(function (array $link) use ($changeUrl): array {
                $link['url'] = $changeUrl(\is_string($link['url']) ? $link['url'] : null);

                return $link;
            })->toArray();

            $this->links = $links;
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

    public function getPageName(): string
    {
        return $this->pageName;
    }

    public function setPageName(string $name): static
    {
        $this->pageName = $name ?: 'page';

        return $this;
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

    /**
     * @return array<string, mixed>
     */
    public function systemViewData(): array
    {
        return $this->toArray();
    }

    /**
     * @return array<string, mixed>
     */
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

    /**
     * @return array<string, mixed>
     */
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
        if ($offset !== null) {
            $this->getData()->put($offset, $value);
        }
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
