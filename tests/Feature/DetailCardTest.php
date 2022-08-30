<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature;

use Leeto\MoonShine\DetailCard\DetailCard;
use Leeto\MoonShine\Fields\Fields;
use Leeto\MoonShine\Tests\TestCase;

class DetailCardTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function it_properties(): void
    {
        $card = DetailCard::make(
            $this->testResource()->fieldsCollection()->detailFields(),
            $this->adminUser()
        );

        $this->assertInstanceOf(Fields::class, $card->fields());
    }
}
