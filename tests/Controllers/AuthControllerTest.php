<?php

declare(strict_types=1);

namespace MoonShine\Tests\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MoonShine\Tests\TestCase;

class AuthControllerTest extends TestCase
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
        $response = $this->authorized()
            ->get(route(config('moonshine.route.prefix').'.login'));

        $response->assertRedirect(route(config('moonshine.route.prefix').'.index'));
    }

    public function test_authenticate()
    {
        $response = $this->post(
            route(config('moonshine.route.prefix').'.authenticate'),
            ['email' => $this->adminUser()->email, 'password' => 'invalid']
        );

        $response->assertInvalid(['email']);

        $response = $this->post(
            route(config('moonshine.route.prefix').'.authenticate'),
            ['email' => $this->adminUser()->email, 'password' => 'test']
        );

        $response->assertValid();
    }

    public function test_logout()
    {
        $response = $this->authorized()
            ->get(route(config('moonshine.route.prefix').'.logout'));

        $response->assertRedirect(route(config('moonshine.route.prefix').'.login'));
    }
}
