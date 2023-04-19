<?php

declare(strict_types=1);

namespace MoonShine\Tests\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MoonShine\Models\MoonshineUser;
use MoonShine\Models\MoonshineUserRole;
use MoonShine\Tests\TestCase;

class CrudControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index(): void
    {
        $resource = $this->testResource();

        $response = $this->authorized()
            ->get($resource->route('index'));

        $response->assertOk();
        $response->assertViewIs('moonshine::crud.index');
        $response->assertViewHas('resource', $resource);

        $response = $this->authorized()
            ->get($resource->route('create'));

        $response->assertOk();
        $response->assertViewIs('moonshine::crud.form');
        $response->assertViewHas('resource', $resource);
    }

    public function test_create(): void
    {
        $resource = $this->testResource();

        $response = $this->authorized()
            ->get($resource->route('create'));

        $response->assertOk();
        $response->assertViewIs('moonshine::crud.form');
    }

    public function test_edit(): void
    {
        $resource = $this->testResource();

        $response = $this->authorized()
            ->get($resource->route('edit', $this->adminUser()->id));

        $response->assertOk();
        $response->assertViewIs('moonshine::crud.form');
        $response->assertViewHas('item', $this->adminUser());
    }

    public function test_store(): void
    {
        $resource = $this->testResource();

        $email = uniqid('', true) . '@example.com';

        $this->assertDatabaseMissing('moonshine_users', [
            'email' => $email,
        ]);

        $response = $this->authorized()
            ->post($resource->route('store'), [
                'name' => 'Test user',
                'moonshine_user_role_id' => MoonshineUserRole::DEFAULT_ROLE_ID,
                'email' => $email,
                'password' => 123456,
                'password_repeat' => 123456,
            ]);

        $response->assertValid();

        $response->assertRedirect($resource->route('index'));

        $this->assertDatabaseHas('moonshine_users', [
            'email' => $email,
        ]);
    }

    public function test_show(): void
    {
        $resource = $this->testResource();

        $response = $this->authorized()
            ->get($resource->route('show', $this->adminUser()->id));

        $response->assertValid();
    }
    public function test_update(): void
    {
        $resource = $this->testResource();

        $this->assertDatabaseMissing('moonshine_users', [
            'name' => 'Admin updated',
        ]);

        $response = $this->authorized()
            ->put($resource->route('update', $this->adminUser()->id), [
                'name' => 'Admin updated',
                'moonshine_user_role_id' => MoonshineUserRole::DEFAULT_ROLE_ID,
                'email' => $this->adminUser()->email,
            ]);

        $response->assertValid();

        $response->assertRedirect($resource->route('index'));

        $this->assertDatabaseHas('moonshine_users', [
            'name' => 'Admin updated',
        ]);
    }

    public function test_destroy(): void
    {
        $resource = $this->testResource();

        $user = MoonshineUser::factory()->createOne([
            'id' => 2,
        ]);

        $this->assertModelExists($user);

        $response = $this->authorized()
            ->delete($resource->route('destroy', $user->id));

        $response->assertRedirect($resource->route('index'));

        $this->assertModelMissing($user);
    }

    /**
     * @test
     * @return void
     */
    public function it_updated_column(): void
    {
        $response = $this->authorized()
            ->putJson(route('moonshine.update-column'), [
                'model' => MoonshineUser::class,
                'key' => $this->adminUser()->getKey(),
                'field' => 'name',
                'value' => 'Test update column',
            ]);

        $response->assertNoContent();

        $this->assertDatabaseHas('moonshine_users', [
            'name' => 'Test update column',
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function it_precognition_validation_no_content(): void
    {
        $response = $this->authorized()
            ->put(
                $this->testResource()
                    ->route('update', $this->adminUser()->getKey()),
                [
                        'name' => 'Admin updated',
                        'moonshine_user_role_id' => MoonshineUserRole::DEFAULT_ROLE_ID,
                ],
                [
                    'Precognition' => 'true',
                ]
            );

        $response->assertNoContent();
    }

    /**
     * @test
     * @return void
     */
    public function it_precognition_validation_error(): void
    {
        $response = $this->authorized()
            ->put(
                $this->testResource()
                    ->route('update', $this->adminUser()->getKey()),
                [
                    'name' => 'Admin updated',
            ],
                [
                    'Precognition' => 'true',
                    'Accept' => 'application/json',
                ]
            );

        $response->assertJsonStructure(['errors' => ['moonshine_user_role_id']]);
    }

    /**
     * @test
     * @return void
     */
    public function it_filtered_data(): void
    {
        $data = [
            'name' => 'FilteredTest',
            'created_at' => '2023-01-03'
        ];

        MoonshineUser::factory()->create($data);

        $this->authorized()->get(
            $this->testResource()->route('index', query: [
                'filters' => $data,
            ]),
        )->assertOk()->assertSeeText('FilteredTest');

        $this->authorized()->get(
            $this->testResource()->route('index', query: [
                'filters' => [
                    'created_at' => '2023-01-03'
                ],
            ]),
        )->assertOk()->assertSeeText('FilteredTest');

        $this->authorized()->get(
            $this->testResource()->route('index', query: [
                'filters' => ['created_at' => '2023-01-04'],
            ]),
        )->assertOk()->assertSeeText(__('moonshine::ui.notfound'));
    }

}
