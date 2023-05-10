<?php

use MoonShine\FormActions\FormAction;
use MoonShine\Models\MoonshineUser;

uses()->group('item-actions');

beforeEach(function () {
    $this->label = 'Delete';
    $this->message = 'Done';
    $this->callback = fn($model) => $model->getKey();
    $this->action = FormAction::make($this->label, $this->callback, $this->message);
});

it('new instance', function () {
    $model = MoonshineUser::factory()->create();

    expect($this->action)
        ->toBeInstanceOf(FormAction::class)
        ->message()
        ->toBe($this->message)
        ->label()
        ->toBe($this->label)
        ->callback($model)
        ->toBe($model->getKey());
});

it('confirmation modal', function () {
    expect($this->action)
        ->confirmation()
        ->toBeFalse()
        ->withConfirm()
        ->confirmation()
        ->toBeTrue();
});
