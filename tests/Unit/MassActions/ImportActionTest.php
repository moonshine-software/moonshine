<?php

use MoonShine\Actions\ImportAction;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('mass-actions');

beforeEach(function (): void {
    $this->label = 'Import';
    $this->resource = TestResourceBuilder::new(addRoutes: true);
    $this->action = ImportAction::make($this->label)
        ->setResource($this->resource);
});

it('make new object', function (): void {
    expect($this->action)
        ->toBeInstanceOf(ImportAction::class);
});

it('correct resource', function (): void {
    expect($this->action)
        ->resource()
        ->toBe($this->resource);
});

it('show in dropdown or line', function (): void {
    expect($this->action)
        ->showInLine()
        ->inDropdown()
        ->toBeFalse()
        ->showInDropdown()
        ->inDropdown()
        ->toBeTrue();
});

it('configure storage', function (): void {
    expect($this->action)
        ->dir('/import')
        ->getDir()
        ->toBe('import')
        ->disk('custom')
        ->getDisk()
        ->toBe('custom');
});

it('correct url', function (): void {
    expect($this->action)
        ->url()
        ->toBe($this->resource->route('actions.index', query: [class_basename($this->action) => 1]));
});

it('correct trigger', function (): void {
    expect($this->action)
        ->getTriggerKey()
        ->toBe(class_basename($this->action))
        ->isTriggered()
        ->toBeFalse();
});

it('is triggered', function (): void {
    fakeRequest($this->resource->route('actions.index', query: [class_basename($this->action) => 1]));

    expect($this->action)
        ->isTriggered()
        ->toBeTrue();
});

it('render view', function (): void {
    test()->withViewErrors([]);

    expect($this->action)
        ->render()
        ->toContain($this->label);
});
