<?php

namespace Leeto\MoonShine\Resources;


use Leeto\MoonShine\Fields\ID;
use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Filters\TextFilter;
use Leeto\MoonShine\Models\MoonshineUserRole;

class MoonShineUserRoleResource extends BaseResource
{
	public static string $model = MoonshineUserRole::class;

    public static string $title = 'Роли';

    public string $titleField = 'name';

    public static bool $system = true;

    public function fields(): array
    {
        return [
            ID::make()->sortable()->showOnExport(),
            Text::make('Название', 'name')->required()->showOnExport(),
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
            TextFilter::make('Название', 'name'),
        ];
    }

    public function actions(): array
    {
        return [];
    }
}
