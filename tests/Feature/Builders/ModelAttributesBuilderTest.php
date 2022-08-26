<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature\Builders;

use Leeto\MoonShine\Builders\ModelAttributesBuilder;
use Leeto\MoonShine\Tests\TestCase;

class ModelAttributesBuilderTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function it_build(): void
    {
        $data = (new ModelAttributesBuilder($this->adminUser()))->build();

        $this->assertEquals($this->adminUser()->getKeyName(), $data['_primaryKeyName']);
        $this->assertEquals($this->adminUser()->getKey(), $data['id']);

        $this->assertEquals(
            $this->adminUser()->moonshineUserRole->getKeyName(),
            $data['moonshineUserRole']['_primaryKeyName']
        );
        $this->assertEquals($this->adminUser()->moonshineUserRole->getKey(), $data['moonshineUserRole']['id']);
    }
}
