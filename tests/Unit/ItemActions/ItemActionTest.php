<?php

use MoonShine\ItemActions\ItemAction;
use MoonShine\Models\MoonshineUser;

uses()->group('item-actions');

beforeEach(function (): void {
    $this->label = 'Delete';
    $this->message = 'Done';
    $this->callback = fn ($model) => $model->getKey();
    $this->action = ItemAction::make($this->label, $this->callback, $this->message);
});

it('new instance', function (): void {
    $model = MoonshineUser::factory()->create();

    expect($this->action)
        ->toBeInstanceOf(ItemAction::class)
        ->message()
        ->toBe($this->message)
        ->label()
        ->toBe($this->label)
        ->callback($model)
        ->toBe($model->getKey());
});

it('confirmation modal', function (): void {
    expect($this->action)
        ->isConfirmed()
        ->toBeFalse()
        ->withConfirm()
        ->isConfirmed()
        ->toBeTrue();
});
