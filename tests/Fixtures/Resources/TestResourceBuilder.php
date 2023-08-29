<?php

namespace MoonShine\Tests\Fixtures\Resources;

use MoonShine\MoonShine;

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
