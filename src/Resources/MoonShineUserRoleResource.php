<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Fields\ID;
use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Models\MoonshineUserRole;
use Leeto\MoonShine\RowActions\DeleteRowAction;
use Leeto\MoonShine\RowActions\EditRowAction;
use Leeto\MoonShine\RowActions\ShowRowAction;

final class MoonShineUserRoleResource extends ModelResource
{
    public static string $model = MoonshineUserRole::class;

    public string $column = 'name';

    public function title(): string
    {
        return trans('moonshine::ui.resource.role');
    }

    public function fields(): array
    {
        return [
            ID::make()->sortable()->showOnExport(),
            Text::make(trans('moonshine::ui.resource.role_name'), 'name')
                ->required()->showOnExport(),
        ];
    }

    public function rowActions(Model $item): array
    {
        return [
            ShowRowAction::make(__('moonshine::ui.show')),
            EditRowAction::make(__('moonshine::ui.edit')),
            DeleteRowAction::make(__('moonshine::ui.delete'))
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
        return [];
    }

    public function actions(): array
    {
        return [];
    }
}
