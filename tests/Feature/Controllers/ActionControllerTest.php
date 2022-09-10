<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature\Controllers;

use Leeto\MoonShine\Tests\TestCase;

class ActionControllerTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function it_action_export(): void
    {
        $response = $this->authorized()->get(
            $this->testResource()->route(
                'action', query: ['uri' => 'export-action']
            )
        );

        $response->assertOk()
            ->assertDownload("export-{$this->testResource()->uriKey()}.xlsx");
    }
}
