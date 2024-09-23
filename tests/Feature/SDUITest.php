<?php

declare(strict_types=1);

namespace MoonShine\Tests\Feature;

use MoonShine\Laravel\Http\Controllers\PageController;
use MoonShine\Laravel\Pages\Dashboard;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use MoonShine\Tests\TestCase;

#[CoversMethod(PageController::class, 'structureResponse')]
#[Group('sdui')]
final class SDUITest extends TestCase
{
    #[Test]
    #[TestDox('it structure response with states')]
    public function structureResponseWithStates(): void
    {
        $page = app(Dashboard::class);
        $response = $this->actingAs($this->adminUser(), 'moonshine')->getJson(
            $page->getUrl(),
            headers: [
                'X-MS-Structure' => true,
            ]
        );

        $response
            ->assertOk()
            ->assertExactJsonStructure([
                'type',
                'components',
                'states',
            ])
            ->assertJson(['type' => 'Dashboard'])
        ;
    }

    #[Test]
    #[TestDox('it structure response without states')]
    public function structureResponseWithoutStates(): void
    {
        $page = app(Dashboard::class);
        $response = $this->actingAs($this->adminUser(), 'moonshine')->getJson(
            $page->getUrl(),
            headers: [
                'X-MS-Structure' => true,
                'X-MS-Without-States' => true,
            ]
        );

        $response
            ->assertOk()
            ->assertExactJsonStructure([
                'type',
                'components',
            ])
            ->assertJson(['type' => 'Dashboard'])
        ;
    }

    #[Test]
    #[TestDox('it structure response only layout')]
    public function structureResponseOnlyLayout(): void
    {
        $page = app(Dashboard::class);
        $response = $this->actingAs($this->adminUser(), 'moonshine')->getJson(
            $page->getUrl(),
            headers: [
                'X-MS-Structure' => true,
                'X-MS-Only-Layout' => true,
            ]
        );

        $response
            ->assertOk()
            ->assertExactJsonStructure([
                'type',
                'components',
                'states',
            ])
            ->assertJson(['type' => 'Layout'])
        ;
    }

    #[Test]
    #[TestDox('it structure response without layout')]
    public function structureResponseWithoutLayout(): void
    {
        $page = app(Dashboard::class);
        $response = $this->actingAs($this->adminUser(), 'moonshine')->getJson(
            $page->getUrl(),
            headers: [
                'X-MS-Structure' => true,
                'X-MS-Without-Layout' => true,
            ]
        );

        $response
            ->assertOk()
            ->assertJson([])
        ;
    }
}
