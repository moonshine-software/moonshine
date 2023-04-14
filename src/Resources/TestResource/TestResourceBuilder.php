<?php

namespace MoonShine\Resources\TestResource;

use MoonShine\Fields\Email;
use MoonShine\Fields\ID;
use MoonShine\Fields\Text;

class TestResourceBuilder
{
    public static function buildForCanSeeTest(): MoonshineTestResource
    {
        $testResource = new MoonshineTestResource();
        $testResource->setTestFields([
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
        return $testResource;
    }
}