<?php

use MoonShine\Tests\Fixtures\DetailComponents\TestDetailComponent;

uses()->group('detail-components');

beforeEach(function () {
    $this->component = TestDetailComponent::make('Component');
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
