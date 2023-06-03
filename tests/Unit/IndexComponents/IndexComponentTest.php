<?php

use MoonShine\Tests\Fixtures\IndexComponents\TestIndexComponent;

uses()->group('index-components');

beforeEach(function (): void {
    $this->component = TestIndexComponent::make('Component');
});

it('component methods', function (): void {
    expect($this->component)
        ->label()
        ->toBe('Component')
        ->id()
        ->toBeString();
});
