<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Markdown;
use MoonShine\Fields\Textarea;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Markdown::make('Markdown');
});

it('textarea is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Textarea::class);
});

it('type', function (): void {
    expect($this->field->type())
        ->toBeEmpty();
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.markdown');
});

it('has assets', function (): void {
    expect($this->field->getAssets())
        ->toBeArray()
        ->not->toBeEmpty();
});

it('add option method', function (): void {
    expect($this->field)
        ->addOption('test-config', 1)
        ->getOptions()->toContain(1)
        ->addOption('test-config-string', 'string')
        ->getOptions()->toContain('string')
        ->addOption('test-config-float', 1.2)
        ->getOptions()->toContain(1.2)
        ->addOption('test-config-bool', true)
        ->getOptions()->toContain(true)
        ->addOption('test-config-array', ['array' => 'array'])
        ->getOptions()->toContain(['array' => 'array'])
        ->addOption('test-config-json', json_encode([['json' => 'json']]))
        ->getOptions()->toContain([['json' => 'json']])
    ;
});

it('global config method', function (): void {
    Markdown::setDefaultOption('test-config', 'global-config-value');

    $field = Markdown::make('Markdown');

    expect($field)
        ->getOptions()->toContain('global-config-value');
});

it('toolbar method', function (): void {
    expect($this->field)
        ->toolbar(['toolbar 1', 'toolbar 2'])
        ->getOptions()->toContain(['toolbar 1', 'toolbar 2']);
});

it('apply', function (): void {
    $data = ['markdown' => 'test'];

    fakeRequest(parameters: $data);

    expect(
        $this->field->apply(
            TestResourceBuilder::new()->onSave($this->field),
            new class () extends Model {
                protected $fillable = [
                    'markdown',
                ];
            }
        )
    )
        ->toBeInstanceOf(Model::class)
        ->markdown
        ->toBe($data['markdown'])
    ;
});
