<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Checkbox;
use MoonShine\Fields\SwitchBoolean;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = SwitchBoolean::make('Active');
    $this->item = new class () extends Model {
        public bool $active = true;
    };

    fillFromModel($this->field, $this->item);
});

it('type', function (): void {
    expect($this->field->type())
        ->toBe('checkbox');
});

it('checkbox is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Checkbox::class);
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.switch');
});

it('preview with not auto update', function (): void {

    expect($this->field->autoUpdate(null, false)->preview())
        ->toBe(
            view('moonshine::fields.switch', [
                'element' => $this->field,
            ])->render()
        );
});

it('preview with auto update', function (): void {
    expect($this->field->preview())
        ->toBe(
            view('moonshine::fields.switch', [
                'element' => $this->field,
                'autoUpdate' => true,
                'item' => $this->item,
            ])->render()
        );
});

it('apply', function (): void {
    $data = ['active' => 1];

    fakeRequest(parameters: $data);

    expect(
        $this->field->apply(
            TestResourceBuilder::new()->onSave($this->field),
            new class () extends Model {
                protected $fillable = [
                    'active'
                ];
            })
        )
        ->toBeInstanceOf(Model::class)
        ->active
        ->toBe($data['active'])
    ;
});