<?php

use MoonShine\Tests\Fixtures\FormComponents\TestFormComponent;

uses()->group('form-components');

beforeEach(function () {
    $this->component = TestFormComponent::make('Component');
});

it('component methods', function () {
    expect($this->component)
        ->label()
        ->toBe('Component')
        ->id()
        ->toBe(str('Component')->slug('_')->value())
        ->name()
        ->toBe(str('Component')->slug('_')->value());
});
