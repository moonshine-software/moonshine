<?php

declare(strict_types=1);

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestItemResource;

uses()->group('resources-feature');

class MassDeleteScopedItem extends Item
{
    protected $table = 'items';

    protected static function booted(): void
    {
        static::addGlobalScope('visible', static fn (Builder $query) => $query->where('name', '!=', 'Hidden'));
    }
}

class MassDeleteScopedResource extends TestItemResource
{
    protected string $model = MassDeleteScopedItem::class;
}

class MassDeleteItemQueryResource extends TestItemResource
{
    protected function modifyItemQueryBuilder(Builder $builder): Builder
    {
        return $builder->where('name', '!=', 'Hidden');
    }
}

class MassDeleteItemPolicy
{
    public function massDelete(MoonshineUser $user): bool
    {
        return true;
    }

    public function delete(MoonshineUser $user, Item $item): bool
    {
        return $item->name !== 'Protected';
    }
}

class MassDeletePolicyResource extends TestItemResource
{
    protected bool $withPolicy = true;
}

it('mass deletes only items within the scoped item query', function (string $resourceClass): void {
    $visible = createItem(1, 0);
    $hidden = createItem(1, 0);
    $hidden->update(['name' => 'Hidden']);
    $this->moonshineCore->resources([$resourceClass]);
    $resource = $this->moonshineCore->getResources()->findByClass($resourceClass);

    asAdmin()->delete($resource->getRoute('crud.massDelete'), ['ids' => [$visible->getKey(), $hidden->getKey()]])
        ->assertRedirect();

    $this->assertDatabaseMissing('items', ['id' => $visible->getKey()]);
    $this->assertDatabaseHas('items', ['id' => $hidden->getKey()]);
})->with([MassDeleteScopedResource::class, MassDeleteItemQueryResource::class]);

it('refuses the entire mass delete when any item is denied', function (bool $ajax): void {
    Gate::policy(Item::class, MassDeleteItemPolicy::class);
    $allowed = createItem(1, 0);
    $protected = createItem(1, 0);
    $protected->update(['name' => 'Protected']);
    $this->moonshineCore->resources([MassDeletePolicyResource::class]);
    $resource = $this->moonshineCore->getResources()->findByClass(MassDeletePolicyResource::class);

    asAdmin()->withHeaders($ajax ? ['X-Requested-With' => 'XMLHttpRequest'] : [])
        ->delete($resource->getRoute('crud.massDelete'), ['ids' => [$allowed->getKey(), $protected->getKey()]])
        ->assertForbidden();

    $this->assertDatabaseHas('items', ['id' => $allowed->getKey()]);
    $this->assertDatabaseHas('items', ['id' => $protected->getKey()]);
})->with([false, true]);

it('mass deletes items allowed by their individual delete policy', function (): void {
    Gate::policy(Item::class, MassDeleteItemPolicy::class);
    $first = createItem(1, 0);
    $second = createItem(1, 0);
    $this->moonshineCore->resources([MassDeletePolicyResource::class]);
    $resource = $this->moonshineCore->getResources()->findByClass(MassDeletePolicyResource::class);

    asAdmin()->delete($resource->getRoute('crud.massDelete'), ['ids' => [$first->getKey(), $second->getKey()]])
        ->assertRedirect();

    $this->assertDatabaseMissing('items', ['id' => $first->getKey()]);
    $this->assertDatabaseMissing('items', ['id' => $second->getKey()]);
});

it('restores the resource item after a denied mass delete', function (): void {
    Gate::policy(Item::class, MassDeleteItemPolicy::class);
    $initial = createItem(1, 0);
    $protected = createItem(1, 0);
    $protected->update(['name' => 'Protected']);
    $resource = app(MassDeletePolicyResource::class)->setItem($initial);
    asAdmin();

    expect(fn () => $resource->massDelete([$protected->getKey()]))
        ->toThrow(AuthorizationException::class);
    expect($resource->getItem())->toBe($initial);
    $this->assertDatabaseHas('items', ['id' => $protected->getKey()]);
});
