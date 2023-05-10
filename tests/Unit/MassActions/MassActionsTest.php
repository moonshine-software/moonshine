<?php

use MoonShine\Actions\ExportAction;
use MoonShine\Actions\MassActions;

uses()->group('mass-actions');

beforeEach(function () {
    $this->actions = MassActions::make([]);
});

it('merge if not exists', function () {
    $this->actions->mergeIfNotExists(ExportAction::make('Export'));

    expect($this->actions)
        ->toBeIterable()
        ->count()
        ->toBe(1)
        ->first()
        ->toBeInstanceOf(ExportAction::class);

    $this->actions->mergeIfNotExists(ExportAction::make('Export'));

    expect($this->actions)
        ->count()
        ->toBe(1)
        ->first()
        ->toBeInstanceOf(ExportAction::class);
});

it('only visible actions', function () {
    $this->actions->mergeIfNotExists(ExportAction::make('Export')->canSee(fn () => false));

    expect($this->actions)
        ->count()
        ->toBe(1)
        ->and($this->actions->onlyVisible())
        ->toBeEmpty();
});

it('only in dropdown/in line', function () {
    $this->actions->mergeIfNotExists(ExportAction::make('Export')->showInDropdown());

    expect($this->actions->inDropdown())
        ->count()
        ->toBe(1)
        ->and($this->actions->inLine())
        ->toBeEmpty();
});
