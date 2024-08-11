<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Resources;

use MoonShine\Laravel\Models\MoonshineUserRole;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

#[Icon('bookmark')]
class MoonShineUserRoleResource extends ModelResource
{
    public string $model = MoonshineUserRole::class;

    public string $column = 'name';

    protected bool $createInModal = true;

    protected bool $detailInModal = true;

    protected bool $editInModal = true;

    protected bool $simplePaginate = true;

    public function getTitle(): string
    {
        return __('moonshine::ui.resource.role');
    }

    protected function indexButtons(): ListOf
    {
        return parent::indexButtons()->except(fn (ActionButton $btn): bool => $btn->getName() === 'detail-button');
    }

    protected function indexFields(): array
    {
        return [
            ID::make()->sortable(),
            Text::make(__('moonshine::ui.resource.role_name'), 'name'),
        ];
    }

    protected function detailFields(): array
    {
        return $this->indexFields();
    }

    protected function formFields(): array
    {
        return [
            Box::make([
                ID::make()->sortable(),
                Text::make(__('moonshine::ui.resource.role_name'), 'name')
                    ->required(),
            ]),
        ];
    }

    /**
     * @return array{name: string}
     */
    protected function rules($item): array
    {
        return [
            'name' => ['required', 'min:5'],
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'name',
        ];
    }
}
