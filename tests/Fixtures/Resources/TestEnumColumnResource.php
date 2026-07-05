<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources;

use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Tests\Fixtures\Enums\TestEnumLabeled;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

class TestEnumColumnResource extends AbstractTestingResource
{
    protected string $model = Item::class;

    public string $title = 'Enum column items';

    protected string $column = 'status';

    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Name title', 'name'),
            Enum::make('Status', 'status')->attach(TestEnumLabeled::class),
        ];
    }

    protected function formFields(): iterable
    {
        return $this->indexFields();
    }

    protected function detailFields(): iterable
    {
        return $this->indexFields();
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [];
    }
}
