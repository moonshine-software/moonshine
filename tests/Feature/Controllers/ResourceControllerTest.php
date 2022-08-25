<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature\Controllers;

use Laravel\Sanctum\Sanctum;
use Leeto\MoonShine\Resources\MoonShineUserResource;
use Leeto\MoonShine\Resources\Resource;
use Leeto\MoonShine\Tests\TestCase;

class ResourceControllerTest extends TestCase
{
    protected Resource $resource;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resource = new MoonShineUserResource();
    }

    public function test_unauthorized()
    {
        $response = $this->getJson($this->resource->route('index'));

        $response->assertStatus(401);
    }

    public function test_index()
    {
        Sanctum::actingAs(
            $this->user,
            ['*'],
            'moonshine'
        );

        $response = $this->getJson($this->resource->route('index'));

        $response->assertJsonPath('title', $this->resource->title());
    }

    public function test_create()
    {
        Sanctum::actingAs(
            $this->user,
            ['*'],
            'moonshine'
        );

        $response = $this->getJson($this->resource->route('create'));

        $response->assertJsonPath('title', $this->resource->title());
    }

    public function test_edit()
    {
        Sanctum::actingAs(
            $this->user,
            ['*'],
            'moonshine'
        );

        $response = $this->getJson($this->resource->route('edit', $this->user->id));

        $response->assertJsonPath('title', $this->resource->title());
    }

    public function test_store()
    {
        Sanctum::actingAs(
            $this->user,
            ['*'],
            'moonshine'
        );

        $this->assertDatabaseMissing('moonshine_users', ['name' => 'Created']);

        $response = $this->postJson(
            $this->resource->route('store', $this->user->id),
            [
                'moonshine_user_role_id' => $this->user->moonshine_user_role_id,
                'name' => 'Created',
                'email' => 'created@test.com',
                'password' => 'test_test',
                'password_repeat' => 'test_test'
            ]
        );

        $response->assertOk();

        $this->assertDatabaseHas('moonshine_users', ['name' => 'Created']);
    }

    public function test_update()
    {
        Sanctum::actingAs(
            $this->user,
            ['*'],
            'moonshine'
        );

        $this->assertModelExists($this->user);
        $this->assertDatabaseHas('moonshine_users', ['name' => 'Admin']);

        $response = $this->putJson(
            $this->resource->route('update', $this->user->id),
            [
                'moonshine_user_role_id' => $this->user->moonshine_user_role_id,
                'name' => 'Updated'
            ]
        );

        $response->assertOk();

        $this->assertDatabaseHas('moonshine_users', ['name' => 'Updated']);
    }

    public function test_destroy()
    {
        Sanctum::actingAs(
            $this->user,
            ['*'],
            'moonshine'
        );

        $this->assertModelExists($this->user);

        $response = $this->deleteJson($this->resource->route('destroy', $this->user->id));
        $response->assertOk();

        $this->assertModelMissing($this->user);
    }
}
