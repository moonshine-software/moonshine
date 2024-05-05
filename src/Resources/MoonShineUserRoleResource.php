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

    protected bool $isAsync = true;

    protected bool $createInModal = true;

    protected bool $editInModal = true;

    public function title(): string
    {
        return __('moonshine::ui.resource.role');
    }

    public function fields(): array
    {
        return [
            Box::make([
                ID::make()->sortable()->showOnExport(),
                Text::make(__('moonshine::ui.resource.role_name'), 'name')
                    ->required()
                    ->showOnExport(),
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
