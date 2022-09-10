<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature\ValueEntities;

use Leeto\MoonShine\Contracts\ValueEntityContract;
use Leeto\MoonShine\Tests\TestCase;
use Leeto\MoonShine\ValueEntities\ModelValueEntityBuilder;

class ModelValueEntityTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function it_build(): void
    {
        $data = (new ModelValueEntityBuilder($this->adminUser()))->build();

        $this->assertInstanceOf(ValueEntityContract::class, $data);

        $this->assertEquals($this->adminUser()->getKeyName(), $data->primaryKeyName());
        $this->assertEquals($this->adminUser()->getKey(), $data->primaryKey());

        $this->assertEquals(
            $this->adminUser()->moonshineUserRole->getKeyName(),
            $data->attributes('moonshineUserRole')->primaryKeyName()
        );

        $this->assertEquals(
            $this->adminUser()->moonshineUserRole->getKey(),
            $data->attributes('moonshineUserRole')->primaryKey()
        );

        $this->assertEquals($this->adminUser()->moonshineUserRole->getKey(), $data->moonshineUserRole->primaryKey());
    }
}
