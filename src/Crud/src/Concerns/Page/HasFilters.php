<?php

declare(strict_types=1);

namespace MoonShine\Crud\Concerns\Page;

use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Crud\Collections\Fields;
use MoonShine\Crud\Contracts\Page\IndexPageContract;
use MoonShine\Crud\Exceptions\FilterException;
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
 * @template TFields of Fields = Fields
 *
 * @mixin IndexPageContract<TFields>
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
        return $this->filters() !== [];
    }

    /**
     * @throws Throwable
     *
     * @return TFields
     */
    public function getFilters(): FieldsContract
    {
        /** @var TFields $collection */
        $collection = $this->getCore()->getFieldsCollection($this->filters());

        $filters = $collection
            ->withoutOutside()
            ->wrapNames($this->getResource()->getQueryParamName('filter'));

        $filters->each(function ($filter): void {
            if (\in_array($filter::class, $this->getIgnoredFields(), true)) {
                throw FilterException::notAcceptable($filter::class);
            }
        });

        return $filters;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getFilterParams(): array
    {
        return $this->getResource()->getFilterParams();
    }
}
