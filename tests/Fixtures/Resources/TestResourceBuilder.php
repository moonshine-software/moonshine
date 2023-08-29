<?php

namespace MoonShine\Tests\Fixtures\Resources;

use MoonShine\Decorations\Block;
use MoonShine\Decorations\Tab;
use MoonShine\Decorations\Tabs;
use MoonShine\Fields\Date;
use MoonShine\Fields\Email;
use MoonShine\Fields\File;
use MoonShine\Fields\ID;
use MoonShine\Fields\Image;
use MoonShine\Fields\Relationships\HasOne;
use MoonShine\Fields\Relationships\MorphMany;
use MoonShine\Fields\Text;
use MoonShine\Fields\TinyMce;
use MoonShine\MoonShine;
use MoonShine\Tests\Fixtures\Models\Category;

class TestResourceBuilder
{
    public static function new(string $model = null): TestResource
    {
        $resource = new TestResource();

        if ($model) {
            $resource->setTestModel($model);
        }

        MoonShine::addResource($resource);

        return $resource;
    }
}
