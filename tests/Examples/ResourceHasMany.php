<?php

declare(strict_types=1);

namespace MoonShine\Tests\Examples;

use MoonShine\Fields\HasMany;
use MoonShine\Fields\Json;
use MoonShine\Fields\Text;
use MoonShine\Models\MoonshineUser;
use MoonShine\Resources\Resource;

class ResourceHasMany extends Resource
{
    public static string $model = MoonshineUser::class;

    public static string $title = 'ResourceHasMany';

    public function fields(): array
    {
        return [
            HasMany::make('Roles')->fields([
                Json::make('Roles 2')->fields([
                    Text::make('Name'),
                ]),

                HasMany::make('Roles 2')->fields([
                    HasMany::make('Roles 3')->fields([
                        Text::make('Name'),
                    ]),
                ]),
            ]),
        ];
    }

    public function rules($item): array
    {
        return [];
    }

    public function search(): array
    {
        return [];
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
