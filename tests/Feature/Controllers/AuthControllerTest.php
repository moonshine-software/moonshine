<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature\Controllers;

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
            route(config('moonshine.prefix').'.authenticate'),
            ['email' => $this->adminUser()->email, 'password' => 'invalid']
        );

        $response->assertInvalid(['email']);

        $response = $this->postJson(
            route(config('moonshine.prefix').'.authenticate'),
            ['email' => $this->adminUser()->email, 'password' => 'test']
        );

        $response->assertNoContent();
    }

    /**
     * @test
     * @return void
     */
    public function it_logout(): void
    {
        $response = $this->actingAs($this->adminUser(), 'moonshine')
            ->deleteJson(route(config('moonshine.prefix').'.logout'));

        $response->assertNoContent();
    }

}
