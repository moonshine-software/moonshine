<?php

use MoonShine\BulkActions\BulkAction;
use MoonShine\Models\MoonshineUser;

uses()->group('mass-actions');

beforeEach(function (): void {
    $this->label = 'MassDelete';
    $this->message = 'Done';
    $this->callback = fn ($model) => $model->getKey();
    $this->action = BulkAction::make($this->label, $this->callback, $this->message);
});

it('new instance', function (): void {
    $model = MoonshineUser::factory()->create();

    expect($this->action)
        ->toBeInstanceOf(BulkAction::class)
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
