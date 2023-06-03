<?php

use MoonShine\Tests\Fixtures\FormComponents\TestFormComponent;

uses()->group('form-components');

beforeEach(function (): void {
    $this->component = TestFormComponent::make('Component');
});

it('component methods', function (): void {
    expect($this->component)
        ->label()
        ->toBe('Component')
        ->id()
        ->toBeString();
});
