<?php

declare(strict_types=1);

namespace MoonShine\Resources;

use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Text;
use MoonShine\Filters\TextFilter;
use MoonShine\Models\MoonshineUserRole;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\Pages\Crud\IndexPage;

class MoonShineUserRoleResource extends ModelResource
{
    public string $model = MoonshineUserRole::class;

    public string $titleField = 'name';

    public static bool $withPolicy = true;

    public function fields(): array
    {
        return [
            Block::make('', [
                ID::make()->sortable()->showOnExport(),
                Text::make(trans('moonshine::ui.resource.role_name'), 'name')
                    ->required()
                    ->showOnExport(),
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

    public function pages(): array
    {
        return [
            IndexPage::make('Роли'),

            FormPage::make(
                request('item')
                    ? 'Редактировать'
                    : 'Добавить'
            ),
        ];
    }
}
