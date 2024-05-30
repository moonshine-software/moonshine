<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Fields\Password;
use MoonShine\UI\Fields\Text;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Password::make('Password');
    $this->item = new class () extends Model {
        public string $password = '';
    };

    fillFromModel($this->field, $this->item);
});

it('text field is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Text::class);
});

it('type', function (): void {
    expect($this->field->attributes()->get('type'))
        ->toBe('password');
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.input');
});

it('preview value', function (): void {
    expect($this->field->preview())
        ->toBe('***');
});

it('apply', function (): void {
    $data = ['password' => '12345'];

    fakeRequest(parameters: $data);

    expect(
        $item = $this->field->apply(
            TestResourceBuilder::new()->onSave($this->field),
            new class () extends Model {
                protected $fillable = [
                    'password',
                ];
            }
        )
    )
        ->toBeInstanceOf(Model::class)
        ->and(Hash::check($data['password'], $item->password))
        ->toBeTrue()
    ;
});
