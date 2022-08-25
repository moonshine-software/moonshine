<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature\Builders;

use Leeto\MoonShine\Builders\ModelAttributesBuilder;
use Leeto\MoonShine\Models\MoonshineUser;
use Leeto\MoonShine\Tests\TestCase;

class ModelAttributesBuilderTest extends TestCase
{
    public function test_build()
    {
        $model = MoonshineUser::query()->first();

        $data = (new ModelAttributesBuilder($model))->build();

        $this->assertEquals($model->getKeyName(), $data['_primaryKeyName']);
        $this->assertEquals($model->getKey(), $data['id']);

        $this->assertEquals($model->moonshineUserRole->getKeyName(), $data['moonshineUserRole']['_primaryKeyName']);
        $this->assertEquals($model->moonshineUserRole->getKey(), $data['moonshineUserRole']['id']);
    }
}
