<?php

declare(strict_types=1);

namespace MoonShine\Resources;

use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Text;
use MoonShine\Filters\TextFilter;
use MoonShine\Models\MoonshineUserRole;

class MoonShineUserRoleResource extends Resource
{
    public static string $model = MoonshineUserRole::class;
    protected static bool $system = true;
    public string $titleField = 'name';
    protected bool $createInModal = true;

    protected bool $editInModal = true;

    public function title(): string
    {
        return trans('moonshine::ui.resource.role');
    }

    public function fields(): array
    {
        return [
            Block::make(trans('moonshine::ui.resource.main_information'), [
                ID::make()
                    ->sortable()
                    ->showOnExport(),

                Text::make(trans('moonshine::ui.resource.role_name'), 'name')
                    ->required()
                    ->showOnExport(),
            ]),
        ];
    }

    /**
     * @return array{name: string[]}
     */
    public function rules($item): array
    {
        return [
            'name' => ['required', 'min:5'],
        ];
    }

    public function search(): array
    {
        return ['id', 'name'];
    }

    public function filters(): array
    {
        return [
            TextFilter::make(trans('moonshine::ui.resource.role_name'), 'name'),
        ];
    }

    public function actions(): array
    {
        return [];
    }
}
