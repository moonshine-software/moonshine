<?php

namespace Leeto\MoonShine\Tests\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Leeto\MoonShine\Models\MoonshineUser;
use Leeto\MoonShine\MoonShine;
use Leeto\MoonShine\Resources\MoonShineUserResource;
use Leeto\MoonShine\Tests\TestCase;

class BaseControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testBaseController()
    {
        config(Arr::dot(config('moonshine.auth', []), 'auth.'));

        app(MoonShine::class)->registerResources([
            MoonShineUserResource::class
        ]);

        $resource = new MoonShineUserResource();

        $user = MoonshineUser::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('test')
        ]);

        $response = $this->actingAs($user, config('moonshine.auth.guard'))
            ->get($resource->route('index'));

        $response->assertOk();
        $response->assertViewIs('moonshine::base.index');
        $response->assertViewHas('resource', $resource);

        $response = $this->actingAs($user, config('moonshine.auth.guard'))
            ->get($resource->route('create'));

        $response->assertOk();
        $response->assertViewIs('moonshine::base.form');
        $response->assertViewHas('resource', $resource);
    }
}