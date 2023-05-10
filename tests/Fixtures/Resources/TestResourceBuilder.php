<?php

namespace MoonShine\Tests\Fixtures\Resources;

use MoonShine\Fields\Email;
use MoonShine\Fields\ID;
use MoonShine\Fields\Text;

class TestResourceBuilder
{
    public static function new(string $model = null, bool $addRoutes = false): TestResource
    {
        $resource = new TestResource();

        if ($model) {
            $resource->setTestModel($model);
        }

        if ($addRoutes) {
            $resource->addRoutes();
        }

        return $resource;
    }

    public static function buildForCanSeeTest(): TestResource
    {
        return self::new()->setTestFields([
            ID::make()
                ->sortable()
                ->showOnExport(),

            Text::make('Name', 'name')
                ->canSee(fn($item) => $item->id === 2)
                ->showOnExport(),

            Email::make('Email', 'email')
                ->sortable()
                ->showOnExport()
                ->required(),
        ]);
    }

    public static function buildWithFields(): TestResource
    {
        return self::new()->setTestFields([
            ID::make(),
            Text::make('Name', 'name'),
        ]);
    }
}
