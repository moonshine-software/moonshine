<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Leeto\MoonShine\Models\MoonshineUser;
use Leeto\MoonShine\Models\MoonshineUserRole;
use Leeto\MoonShine\Resources\MoonShineUserResource;
use Leeto\MoonShine\Tests\TestCase;

class MoonShineUserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index(): void
    {
        $resource = new MoonShineUserResource();

        $response = $this->actingAs($this->user, config('moonshine.auth.guard'))
            ->get($resource->route('index'));

        $response->assertOk();
        $response->assertViewIs('moonshine::crud.index');
        $response->assertViewHas('resource', $resource);

        $response = $this->actingAs($this->user, config('moonshine.auth.guard'))
            ->get($resource->route('create'));

        $response->assertOk();
        $response->assertViewIs('moonshine::crud.form');
        $response->assertViewHas('resource', $resource);
    }

    public function test_create(): void
    {
        $resource = new MoonShineUserResource();

        $response = $this->actingAs($this->user, config('moonshine.auth.guard'))
            ->get($resource->route('create'));

        $response->assertOk();
        $response->assertViewIs('moonshine::crud.form');
    }

    public function test_edit(): void
    {
        $resource = new MoonShineUserResource();

        $response = $this->actingAs($this->user, config('moonshine.auth.guard'))
            ->get($resource->route('edit', $this->user->id));

        $response->assertOk();
        $response->assertViewIs('moonshine::crud.form');
        $response->assertViewHas('item', $this->user);
    }

    public function test_store(): void
    {
        $resource = new MoonShineUserResource();
        $email = uniqid('', true) . '@example.com';

        $this->assertDatabaseMissing('moonshine_users', [
            'email' => $email,
        ]);

        $response = $this->actingAs($this->user, config('moonshine.auth.guard'))
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
        $resource = new MoonShineUserResource();

        $response = $this->actingAs($this->user, config('moonshine.auth.guard'))
            ->get($resource->route('show', $this->user->id));

        $response->assertValid();
    }
    public function test_update(): void
    {
        $resource = new MoonShineUserResource();

        $this->assertDatabaseMissing('moonshine_users', [
            'name' => 'Admin updated',
        ]);

        $response = $this->actingAs($this->user, config('moonshine.auth.guard'))
            ->put($resource->route('update', $this->user->id), [
                'name' => 'Admin updated',
                'moonshine_user_role_id' => MoonshineUserRole::DEFAULT_ROLE_ID,
                'email' => $this->user->email,
            ]);

        $response->assertValid();

        $response->assertRedirect($resource->route('index'));

        $this->assertDatabaseHas('moonshine_users', [
            'name' => 'Admin updated',
        ]);
    }

    public function test_destroy(): void
    {
        $resource = new MoonShineUserResource();

        $user = MoonshineUser::factory()->createOne([
            'id' => 2,
        ]);

        $this->assertModelExists($user);

        $response = $this->actingAs($this->user, config('moonshine.auth.guard'))
            ->delete($resource->route('destroy', $user->id));

        $response->assertRedirect($resource->route('index'));

        $this->assertModelMissing($user);
    }
}
