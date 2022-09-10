<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature\Controllers;

use Leeto\MoonShine\Tests\TestCase;

class CrudControllerTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function it_unauthorized(): void
    {
        $response = $this->getJson($this->testResource()->route('index'));

        $response->assertUnauthorized();
    }

    /**
     * @test
     * @return void
     */
    public function it_index_response(): void
    {
        $response = $this->authorized()
            ->getJson($this->testResource()->route('index'));

        $response->assertJsonPath('resource.title', $this->testResource()->title());
    }

    /**
     * @test
     * @return void
     */
    public function it_show_response(): void
    {
        $response = $this->authorized()
            ->getJson(
                $this->testResource()->route(
                    'show',
                    $this->adminUser()->getKey()
                )
            );

        $response->assertJsonPath('resource.title', $this->testResource()->title());
    }

    /**
     * @test
     * @return void
     */
    public function it_create_response(): void
    {
        $response = $this->authorized()
            ->getJson($this->testResource()->route('create'));

        $response->assertJsonPath('resource.title', $this->testResource()->title());
    }

    /**
     * @test
     * @return void
     */
    public function it_edit_response(): void
    {
        $response = $this->authorized()
            ->getJson($this->testResource()->route('edit', $this->adminUser()->getKey()));

        $response->assertJsonPath('resource.title', $this->testResource()->title());
    }

    /**
     * @test
     * @return void
     */
    public function it_store_response(): void
    {
        $this->assertDatabaseMissing('moonshine_users', ['name' => 'Created']);

        $response = $this->authorized()
            ->postJson(
                $this->testResource->route('store', $this->adminUser()->getKey()),
                [
                    'moonshine_user_role_id' => $this->adminUser()->moonshine_user_role_id,
                    'name' => 'Created',
                    'email' => 'created@test.com',
                    'password' => 'test_test',
                    'password_repeat' => 'test_test'
                ]
            );

        $response->assertCreated();

        $this->assertDatabaseHas('moonshine_users', ['name' => 'Created']);
    }

    /**
     * @test
     * @return void
     */
    public function it_update_response(): void
    {
        $this->assertModelExists($this->adminUser());
        $this->assertDatabaseHas('moonshine_users', ['name' => 'Admin']);

        $response = $this->authorized()
            ->putJson(
                $this->testResource()->route('update', $this->adminUser()->getKey()),
                [
                    'moonshine_user_role_id' => $this->adminUser()->moonshine_user_role_id,
                    'name' => 'Updated'
                ]
            );

        $response->assertOk();

        $this->assertDatabaseHas('moonshine_users', ['name' => 'Updated']);
    }

    /**
     * @test
     * @return void
     */
    public function it_destroy_response(): void
    {
        $this->assertModelExists($this->adminUser());

        $response = $this->authorized()
            ->deleteJson($this->testResource()->route('destroy', $this->adminUser()->getKey()));

        $response->assertOk();

        $this->assertModelMissing($this->adminUser());
    }
}
