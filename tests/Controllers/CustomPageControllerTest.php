<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Controllers;

use Leeto\MoonShine\Tests\TestCase;

final class CustomPageControllerTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function it_success_response(): void
    {
        $this->authorized()
            ->get(route('moonshine.custom_page', 'profile'))
            ->assertOk()
            ->assertSeeText('Profile');
    }
}
