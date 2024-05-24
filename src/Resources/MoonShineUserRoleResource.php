<?php

declare(strict_types=1);

namespace MoonShine\Resources;

use MoonShine\Attributes\Icon;
use MoonShine\Components\Layout\Box;
use MoonShine\Fields\ID;
use MoonShine\Fields\Text;
use MoonShine\Models\MoonshineUserRole;

#[Icon('bookmark')]
class MoonShineUserRoleResource extends ModelResource
{
    public string $model = MoonshineUserRole::class;

    public string $column = 'name';

    protected bool $createInModal = true;

    protected bool $detailInModal = true;

    protected bool $editInModal = true;

    public function title(): string
    {
        return __('moonshine::ui.resource.role');
    }

    public function indexFields(): array
    {
        return [
            ID::make()->sortable(),
            Text::make(__('moonshine::ui.resource.role_name'), 'name'),
        ];
    }

    public function detailFields(): array
    {
        return $this->indexFields();
    }

    public function formFields(): array
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
    public function rules($item): array
    {
        return [
            'name' => 'required|min:5',
        ];
    }

    public function search(): array
    {
        return [
            'id',
            'name',
        ];
    }
}
