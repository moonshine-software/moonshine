<?php

declare(strict_types=1);

namespace MoonShine\Crud\Concerns\Resource;

use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Crud\Contracts\HasQueryTagsContract;
use MoonShine\Crud\Pages\IndexPage;
use MoonShine\Crud\QueryTags\QueryTag;

/**
 * @mixin CrudResourceContract
 * @deprecated Will be removed in 5.0
 * @see IndexPage
 */
trait HasQueryTags
{
    /**
     * @return list<QueryTag>
     */
    public function getQueryTags(): array
    {
        if ($this->getIndexPage() instanceof HasQueryTagsContract && $this->getIndexPage()->hasQueryTags()) {
            return $this->getIndexPage()->getQueryTags();
        }

        return Collection::make($this->queryTags())
            ->map(fn (QueryTag $queryTag): QueryTag => $queryTag->prefix($this->getQueryParamPrefix()))
            ->toArray();
    }

    /**
     * Get an array of custom form actions
     *
     * @return list<QueryTag>
     */
    protected function queryTags(): array
    {
        return [];
    }

    public function hasQueryTags(): bool
    {
        if ($this->queryTags() !== []) {
            return true;
        }

        return $this->getIndexPage() instanceof HasQueryTagsContract && $this->getIndexPage()->hasQueryTags();
    }
}
