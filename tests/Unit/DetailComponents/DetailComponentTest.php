<?php

use MoonShine\Tests\Fixtures\DetailComponents\TestDetailComponent;

uses()->group('detail-components');

beforeEach(function (): void {
    $this->component = TestDetailComponent::make('Component');
});

it('component methods', function (): void {
    expect($this->component)
        ->label()
        ->toBe('Component')
        ->id()
        ->toBeString();
});
