<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature\Controllers;

use Leeto\MoonShine\Tests\TestCase;

class ResourceControllerTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function it_unauthorized(): void
    {
        $response = $this->getJson($this->testResource()->route('index'));

        $response->assertStatus(401);
    }

    /**
     * @test
     * @return void
     */
    public function it_index_response(): void
    {
        $response = $this->actingAs($this->adminUser(), 'moonshine')
            ->getJson($this->testResource()->route('index'));

        $response->assertJsonPath('title', $this->testResource()->title());
    }

    /**
     * @test
     * @return void
     */
    public function it_create_response(): void
    {
        $response = $this->actingAs($this->adminUser(), 'moonshine')
            ->getJson($this->testResource()->route('create'));

        $response->assertJsonPath('title', $this->testResource()->title());
    }

    /**
     * @test
     * @return void
     */
    public function it_edit_response(): void
    {
        $response = $this->actingAs($this->adminUser(), 'moonshine')
            ->getJson($this->testResource()->route('edit', $this->adminUser()->id));

        $response->assertJsonPath('title', $this->testResource()->title());
    }

    /**
     * @test
     * @return void
     */
    public function it_store_response(): void
    {
        $this->assertDatabaseMissing('moonshine_users', ['name' => 'Created']);

        $response = $this->actingAs($this->adminUser(), 'moonshine')
            ->postJson(
                $this->testResource->route('store', $this->adminUser()->id),
                [
                    'moonshine_user_role_id' => $this->adminUser()->moonshine_user_role_id,
                    'name' => 'Created',
                    'email' => 'created@test.com',
                    'password' => 'test_test',
                    'password_repeat' => 'test_test'
                ]
            );

        $response->assertOk();

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

        $response = $this->actingAs($this->adminUser(), 'moonshine')
            ->putJson(
                $this->testResource()->route('update', $this->adminUser()->id),
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

        $response = $this->actingAs($this->adminUser(), 'moonshine')
            ->deleteJson($this->testResource()->route('destroy', $this->adminUser()->id));

        $response->assertOk();

        $this->assertModelMissing($this->adminUser());
    }
}
