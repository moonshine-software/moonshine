<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use MoonShine\Support\Enums\TextWrap;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\FormElement;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\InputExtensions\InputExtension;
use MoonShine\UI\InputExtensions\InputEye;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Text::make('Field name');
});

it('field and form element is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Field::class)
        ->toBeInstanceOf(FormElement::class);
});

it('type', function (): void {
    expect($this->field->getAttributes()->get('type'))
        ->toBe('text');
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.input');
});

it('mask', function (): void {
    expect($this->field->mask('999'))
        ->getMask()
        ->toBe('999');
});

it('extension', function (): void {
    expect($this->field->extension(new InputEye()))
        ->getExtensions()
        ->toBeCollection()
        ->toHaveCount(1)
        ->getExtensions()
        ->each->toBeInstanceOf(InputExtension::class);
});

it('apply', function (): void {
    $data = ['field_name' => 'test'];

    fakeRequest(parameters: $data);

    expect(
        $this->field->apply(
            TestResourceBuilder::new()->fieldApply($this->field),
            new class () extends Model {
                protected $fillable = [
                    'field_name',
                ];
            }
        )
    )
        ->toBeInstanceOf(Model::class)
        ->field_name
        ->toBe($data['field_name'])
    ;
});

it('conditionally escapes on apply without affecting preview', function (string $fieldClass, ?bool $condition, string $expected): void {
    $value = '<script>alert(1)</script> &';
    $field = $fieldClass::make('Field name');

    if (! \is_null($condition)) {
        $field->escapeOnApply(fn (): bool => $condition);
    }

    fakeRequest(parameters: ['field_name' => $value]);

    expect($field->apply(
        TestResourceBuilder::new()->fieldApply($field),
        new class () extends Model {
            protected $fillable = ['field_name'];
        }
    ))
        ->field_name
        ->toBe($expected)
        ->and((string) $field->fill($value)->previewMode()->render())
        ->not->toContain($value)
        ->toContain('&lt;script&gt;');
})->with([
    [Text::class, null, '&lt;script&gt;alert(1)&lt;/script&gt; &amp;'],
    [Text::class, true, '&lt;script&gt;alert(1)&lt;/script&gt; &amp;'],
    [Text::class, false, '<script>alert(1)</script> &'],
    [Textarea::class, null, '&lt;script&gt;alert(1)&lt;/script&gt; &amp;'],
    [Textarea::class, true, '&lt;script&gt;alert(1)&lt;/script&gt; &amp;'],
    [Textarea::class, false, '<script>alert(1)</script> &'],
]);

it('visual states', function () {
    $field = Text::make('Field name')->fill('<p>Hello world</p>');

    expect((string) $field->render())
        ->toContain('input', 'type="text"')
        ->and((string) $field->flushRenderCache()->previewMode()->render())
        ->toBe('<div class="text-ellipsis">&lt;p&gt;Hello world&lt;/p&gt;</div>')
        ->and((string) $field->flushRenderCache()->rawMode()->render())
        ->toBe('<p>Hello world</p>')
        ->and((string) $field->flushRenderCache()->defaultMode()->rawMode()->previewMode()->render())
        ->toContain('input', 'type="text"')
    ;
});

it('add/remove classes', function () {
    $field = Text::make('Field name');
    $field->class('btn-success');

    expect($field->getAttributes()->get('class'))
        ->toContain('btn-success')
    ;

    $field->class('btn-primary');

    expect($field->getAttributes()->get('class'))
        ->toContain('btn-success', 'btn-primary')
    ;

    $field->removeClass('btn-primary');

    expect($field->getAttributes()->get('class'))
        ->toContain('btn-success')
        ->not->toContain('btn-primary')
    ;

    $field->class('btn-primary');
    $field->removeClass('btn-(success|primary)');

    expect($field->getAttributes()->get('class'))
        ->toBe('')
    ;

    $field->class('btn-primary-lg');
    $field->removeClass('btn-primary');

    expect($field->getAttributes()->get('class'))
        ->toBe('btn-primary-lg')
    ;

    $field->class(['test', 'form-control', 'btn-primary', 'btn-primaries', 'primary']);
    $field->removeClass('primary|test');

    expect(Str::of($field->getAttributes()->get('class'))->explode(' '))
        ->toContainEqual('form-control', 'btn-primary', 'btn-primaries', 'btn-primary-lg')
        ->not->toContainEqual('primary')
    ;
});

it('text wrap', function () {
    $field = Text::make('Field name')->previewMode();

    expect($field->render())
        ->toContain('div class="text-ellipsis">')
        ->and($field->flushRenderCache()->textWrap(TextWrap::CLAMP)->render())
        ->toContain('div class="text-clamp">')
        ->and($field->flushRenderCache()->withoutTextWrap()->render())
        ->not->toContain('div class="text-clamp">', 'div class="text-ellipsis">')
    ;
});
