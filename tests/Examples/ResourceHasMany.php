<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Examples;

use Leeto\MoonShine\Fields\HasMany;
use Leeto\MoonShine\Fields\Json;
use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Models\MoonshineUser;
use Leeto\MoonShine\Resources\Resource;

/**
 * todo: просто ресурс
 */
class ResourceHasMany extends Resource
{
    public static string $model = MoonshineUser::class;

    public static string $title = 'ResourceHasMany';

    public function fields(): array
    {
        return [
            HasMany::make('Roles')->fields([
                Json::make('Roles 2')->fields([
                    Text::make('Name')
                ]),

                HasMany::make('Roles 2')->fields([
                    HasMany::make('Roles 3')->fields([
                        Text::make('Name'),
                    ])
                ])
            ])
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
