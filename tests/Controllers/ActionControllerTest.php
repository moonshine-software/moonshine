<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Controllers;

use Leeto\MoonShine\Actions\ExportAction;
use Leeto\MoonShine\Exceptions\ActionException;
use Leeto\MoonShine\Tests\TestCase;

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
