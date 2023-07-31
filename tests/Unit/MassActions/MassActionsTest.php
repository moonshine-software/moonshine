<?php

use MoonShine\Actions\Actions;
use MoonShine\Actions\ExportAction;

uses()->group('mass-actions');

beforeEach(function (): void {
    $this->actions = Actions::make();
});

it('merge if not exists', function (): void {
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

it('only visible actions', function (): void {
    $this->actions->mergeIfNotExists(ExportAction::make('Export')->canSee(fn (): bool => false));

    expect($this->actions)
        ->count()
        ->toBe(1)
        ->and($this->actions->onlyVisible())
        ->toBeEmpty();
});

it('only in dropdown/in line', function (): void {
    $this->actions->mergeIfNotExists(ExportAction::make('Export')->showInDropdown());

    expect($this->actions->inDropdown())
        ->count()
        ->toBe(1)
        ->and($this->actions->inLine())
        ->toBeEmpty();
});
