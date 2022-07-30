<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Leeto\MoonShine\Models\MoonshineUser;
use Leeto\MoonShine\Models\MoonshineUserRole;
use Leeto\MoonShine\Resources\MoonShineUserResource;
use Leeto\MoonShine\Tests\TestCase;

class MoonShineAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page()
    {
        $response = $this->get(route(config('moonshine.route.prefix').'.login'));

        $response->assertOk();
        $response->assertViewIs('moonshine::auth.login');
    }

    public function test_login_redirect_to_dashboard()
    {
        $response = $this->actingAs($this->user, config('moonshine.auth.guard'))
            ->get(route(config('moonshine.route.prefix').'.login'));

        $response->assertRedirect(route(config('moonshine.route.prefix').'.index'));
    }

    public function test_authenticate()
    {
        $response = $this->post(
            route(config('moonshine.route.prefix').'.authenticate'),
            ['email' => $this->user->email, 'password' => 'invalid']
        );

        $response->assertInvalid(['login']);

        $response = $this->post(
            route(config('moonshine.route.prefix').'.authenticate'),
            ['email' => $this->user->email, 'password' => 'test']
        );

        $response->assertValid();
    }

    public function test_logout()
    {
        $response = $this->actingAs($this->user, config('moonshine.auth.guard'))
            ->get(route(config('moonshine.route.prefix').'.logout'));

        $response->assertRedirect(route(config('moonshine.route.prefix').'.login'));
    }

}
