<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature\Controllers;

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
        $response = $this->actingAs($this->user, 'moonshine')->getJson($this->resource->route('index'));

        $response->assertJsonPath('title', $this->resource->title());
    }

    public function test_create()
    {
        $response = $this->actingAs($this->user, 'moonshine')->getJson($this->resource->route('create'));

        $response->assertJsonPath('title', $this->resource->title());
    }

    public function test_edit()
    {
        $response = $this->actingAs($this->user, 'moonshine')->getJson($this->resource->route('edit', $this->user->id));

        $response->assertJsonPath('title', $this->resource->title());
    }

    public function test_store()
    {
        $this->assertDatabaseMissing('moonshine_users', ['name' => 'Created']);

        $response = $this->actingAs($this->user, 'moonshine')->postJson(
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
        $this->assertModelExists($this->user);
        $this->assertDatabaseHas('moonshine_users', ['name' => 'Admin']);

        $response = $this->actingAs($this->user, 'moonshine')->putJson(
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
        $this->assertModelExists($this->user);

        $response = $this->actingAs($this->user, 'moonshine')
            ->deleteJson($this->resource->route('destroy', $this->user->id));
        $response->assertOk();

        $this->assertModelMissing($this->user);
    }
}
