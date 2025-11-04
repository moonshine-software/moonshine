<?php

declare(strict_types=1);

namespace MoonShine\Crud\Concerns\Page;

use Illuminate\Support\Collection;
use MoonShine\Crud\Contracts\Page\IndexPageContract;
use MoonShine\Crud\QueryTags\QueryTag;

/**
 * @mixin IndexPageContract
 */
trait HasQueryTags
{
    /**
     * @return list<QueryTag>
     */
    public function getQueryTags(): array
    {
        $queryParamPrefix = $this->getResource()?->getQueryParamPrefix() ?? '';

        return Collection::make($this->queryTags())
            ->when(
                $queryParamPrefix != '',
                fn (Collection $queryTags): Collection => $queryTags
                    ->map(fn (QueryTag $queryTag): QueryTag => $queryTag->setPrefix($queryParamPrefix))
            )
            ->toArray();
    }

    /**
     * @return list<QueryTag>
     */
    protected function queryTags(): array
    {
        return [];
    }

    public function hasQueryTags(): bool
    {
        return $this->queryTags() !== [];
    }
}
