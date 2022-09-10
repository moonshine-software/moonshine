<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature\Controllers;

use Leeto\MoonShine\MoonShineRouter;
use Leeto\MoonShine\Tests\TestCase;

class AuthControllerTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function it_sanctum_csrf_cookie(): void
    {
        $response = $this->get(route('sanctum.csrf-cookie'));

        $response->assertCookie('XSRF-TOKEN');
    }

    /**
     * @test
     * @return void
     */
    public function it_authenticate(): void
    {
        $response = $this->postJson(
            MoonShineRouter::to('authenticate'),
            ['email' => $this->adminUser()->email, 'password' => 'invalid']
        );

        $response->assertInvalid(['email']);

        $response = $this->postJson(
            MoonShineRouter::to('authenticate'),
            ['email' => $this->adminUser()->email, 'password' => 'test']
        );

        $response->assertOk();
    }

    /**
     * @test
     * @return void
     */
    public function it_check(): void
    {
        $response = $this->getJson(MoonShineRouter::to('authenticate.me'));

        $response->assertUnauthorized();

        $response = $this->authorized()
            ->getJson(MoonShineRouter::to('authenticate.me'));

        $response->assertOk()
            ->assertJsonPath($this->adminUser()->getKeyName(), $this->adminUser()->getKey());
    }

    /**
     * @test
     * @return void
     */
    public function it_logout(): void
    {
        $response = $this->authorized()
            ->deleteJson(MoonShineRouter::to('authenticate.logout'));

        $response->assertNoContent();
    }

}
