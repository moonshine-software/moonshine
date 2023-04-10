<?php

declare(strict_types=1);

namespace MoonShine\Tests\Controllers;

use MoonShine\Actions\ExportAction;
use MoonShine\Exceptions\ActionException;
use MoonShine\Tests\TestCase;

class ActionControllerTest extends TestCase
{
    /**
     * @test
     * @return void
     * @throws ActionException
     */
    public function it_action_export(): void
    {
        $action = ExportAction::make('Export')
            ->setResource($this->testResource);

        $response = $this->authorized()->get(
            $action->url()
        );

        $response->assertOk()
            ->assertDownload("{$this->testResource()->routeNameAlias()}.xlsx");
    }
}
