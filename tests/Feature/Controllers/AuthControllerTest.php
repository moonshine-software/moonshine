<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Leeto\MoonShine\Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticate()
    {
        $response = $this->postJson(
            route(config('moonshine.prefix').'.authenticate'),
            ['email' => $this->user->email, 'password' => 'invalid']
        );

        $response->assertInvalid(['login']);

        $response = $this->postJson(
            route(config('moonshine.prefix').'.authenticate'),
            ['email' => $this->user->email, 'password' => 'test']
        );

        $response->assertOk();
    }

    public function test_logout()
    {
        $response = $this->actingAs($this->user, 'moonshine')->deleteJson(route(config('moonshine.prefix').'.logout'));

        $response->assertNoContent();
    }

}
