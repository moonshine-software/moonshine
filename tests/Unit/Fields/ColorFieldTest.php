<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Fields\Color;
use MoonShine\UI\Fields\Field;

uses()->group('fields');


beforeEach(function (): void {
    $this->field = Color::make('Color');
});

it('text is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Field::class);
});

it('type', function (): void {
    expect($this->field->getAttributes()->get('type'))
        ->toBe('color');
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.color');
});

it('resolve preview value', function (): void {
    expect($this->field->setValue('#DDD')->rawMode()->preview())
        ->toBe('#DDD');
});

it('escapes malicious values when rendered', function (): void {
    $payload = "'-alert(document.domain)-<img src=x onerror=alert(1)>";

    $form = (string) $this->field->fill($payload)->render();
    $preview = (string) $this->field->flushRenderCache()->previewMode()->render();

    expect($form)
        ->toContain('\\u0027-alert(document.domain)-\\u003Cimg src=x onerror=alert(1)\\u003E')
        ->not->toContain("color: '{$payload}'")
        ->not->toContain('<img src=x onerror=alert(1)>')
        ->and($preview)
        ->toContain('&lt;img src=x onerror=alert(1)&gt;')
        ->not->toContain('<img src=x onerror=alert(1)>');
});

it('apply', function (): void {
    $data = ['color' => '#FFF'];

    fakeRequest(parameters: $data);

    expect(
        $this->field->apply(
            TestResourceBuilder::new()->fieldApply($this->field),
            new class () extends Model {
                protected $fillable = [
                    'color',
                ];
            }
        )
    )
        ->toBeInstanceOf(Model::class)
        ->color
        ->toBe($data['color']);
});
