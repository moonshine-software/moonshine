<?php

namespace Leeto\MoonShine\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Fields\ID;
use Leeto\MoonShine\Models\MoonshineUser;
use Leeto\MoonShine\Resources\Resource;

class RelationResource extends Resource
{
    public static string $model = MoonshineUser::class;

    public static string $title = 'MoonshineUser';

    public static bool $withPolicy = true;

    public function scopes(): array
    {
        return [];
    }

    public function fields(): array
    {
        return [
            ID::make()->sortable()
        ];
    }

    public function rules(Model $item): array
    {
        return [];
    }

    public function search(): array
    {
        return ['id'];
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
