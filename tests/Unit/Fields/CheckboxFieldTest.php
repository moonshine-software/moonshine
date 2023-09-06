<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Checkbox;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Checkbox::make('Active');
});

it('type', function (): void {
    expect($this->field->type())
        ->toBe('checkbox');
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.checkbox');
});

it('preview', function (): void {
    expect((string) $this->field)
        ->toBe(view('moonshine::fields.checkbox', ['element' => $this->field])->render());
});

it('correct is checked value', function (): void {

    $this->field->resolveFill(['active' => true]);

    expect($this->field->isChecked())
        ->toBeTrue();
});

it('on/off values', function (): void {
    expect($this->field->onValue('yes')->offValue('no'))
        ->getOnValue()
        ->toBe('yes')
        ->getOffValue()
        ->toBe('no');
});

it('apply', function (): void {
    $data = ['active' => 'false'];

    fakeRequest(parameters: $data);

    expect(
        $this->field->apply(
            TestResourceBuilder::new()->onSave($this->field),
            new class () extends Model {
                protected $fillable = [
                    'active',
                ];
            }
        )
    )
        ->toBeInstanceOf(Model::class)
        ->active
        ->toBe($data['active'])
    ;

});
