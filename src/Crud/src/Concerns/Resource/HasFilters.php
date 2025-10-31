<?php

declare(strict_types=1);

namespace MoonShine\Crud\Concerns\Resource;

use InvalidArgumentException;
use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Crud\Collections\Fields;
use MoonShine\Crud\Contracts\HasFiltersContract;
use MoonShine\Crud\Exceptions\FilterException;
use MoonShine\Crud\Pages\IndexPage;
use MoonShine\UI\Fields\Fieldset;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\HiddenIds;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Password;
use MoonShine\UI\Fields\PasswordRepeat;
use MoonShine\UI\Fields\Position;
use MoonShine\UI\Fields\Preview;
use Throwable;

/**
 * @deprecated Will be removed in 5.0
 * @see IndexPage
 *
 * @template TFields of Fields = Fields
 * @mixin CrudResourceContract
 */
trait HasFilters
{
    /**
     * @var list<class-string<FieldContract>>
     */
    protected array $ignoreFields = [
        HiddenIds::class,
        Position::class,
        File::class,
        Image::class,
        Password::class,
        PasswordRepeat::class,
        Preview::class,
        Fieldset::class,
    ];

    /**
     * @return list<class-string<FieldContract>>
     */
    protected function getIgnoredFields(): array
    {
        return $this->ignoreFields;
    }

    /**
     * @return list<FieldContract>
     */
    protected function filters(): iterable
    {
        return [];
    }

    public function hasFilters(): bool
    {
        if ($this->filters() !== []) {
            return true;
        }

        return $this->getIndexPage() instanceof HasFiltersContract && $this->getIndexPage()->hasFilters();
    }

    /**
     * @throws Throwable
     * @return TFields
     */
    public function getFilters(): FieldsContract
    {
        if ($this->getIndexPage() instanceof HasFiltersContract && $this->getIndexPage()->hasFilters()) {
            return $this->getIndexPage()->getFilters();
        }

        /** @var TFields $collection */
        $collection = $this->getCore()->getFieldsCollection($this->filters());

        $filters = $collection
            ->withoutOutside()
            ->wrapNames($this->getQueryParamName('filter'));

        $filters->each(function ($filter): void {
            if (\in_array($filter::class, $this->getIgnoredFields(), true)) {
                throw FilterException::notAcceptable($filter::class);
            }
        });

        return $filters;
    }

    /**
     * @return array<array-key, mixed>
     * @throws InvalidArgumentException
     */
    public function getFilterParams(): array
    {
        $default = $this->getQueryParam('filter', []);

        if ($this->isSaveQueryState() && ! $this->hasQueryParam('reset')) {
            return data_get(
                $this->getCore()->getCache()->get($this->getQueryCacheKey(), []),
                'filter',
                $default,
            );
        }

        return $default;
    }
}
