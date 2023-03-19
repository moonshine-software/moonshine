<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Resources;

use Leeto\MoonShine\Decorations\Block;
use Leeto\MoonShine\Fields\ID;
use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Filters\TextFilter;
use Leeto\MoonShine\Models\MoonshineUserRole;

class MoonShineUserRoleResource extends Resource
{
    public static string $model = MoonshineUserRole::class;

    public string $titleField = 'name';

    protected static bool $system = true;

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
                ID::make()->sortable()->showOnExport(),
                Text::make(trans('moonshine::ui.resource.role_name'), 'name')
                    ->required()->showOnExport(),
            ]),
        ];
    }

    public function rules($item): array
    {
        return [
            'name' => 'required|min:5',
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
