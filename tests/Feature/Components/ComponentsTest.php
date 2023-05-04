<?php

use Illuminate\Support\ViewErrorBag;
use MoonShine\MoonShine;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('components');

it('menu', function () {
    MoonShine::menu([
        TestResourceBuilder::new()
            ->setTestTitle('Testing menu item')
    ]);

    test()
        ->blade('<x-moonshine::menu-component />')
        ->assertSee('Testing menu item');
});

it('async modal', function () {
    test()
        ->blade('<x-moonshine::async-modal id="async" route="/">
            OuterHtml
        </x-moonshine::async-modal>')
        ->assertSee('OuterHtml');
});

it('form and input', function () {
    test()
        ->blade('<x-moonshine::form>
            <x-moonshine::form.input name="test-input" />
        </x-moonshine::form>')
        ->assertSee('form')
        ->assertSee('input')
        ->assertSee('test-input');
});

it('table with slots', function () {
    test()
        ->blade('<x-moonshine::table>
            <x-slot:thead>
                <th>ID</th>
            </x-slot:thead>
            <x-slot:tbody>
                <tr><td>1</td></tr>
            </x-slot:tbody>
            <x-slot:tfoot>
            <tr><td>footer</td></tr>
            </x-slot:tfoot>
        </x-moonshine::table>')
        ->assertSee('table')
        ->assertSee('ID')
        ->assertSee('thead')
        ->assertSee('tbody')
        ->assertSee('1')
        ->assertSee('tfoot')
        ->assertSee('footer');
});

it('table with values', function () {
    test()
        ->blade('<x-moonshine::table :values="$values" :columns="$columns" />', [
            'columns' => ['id' => 'ID'],
            'values' => [['id' => 1]]
        ])
        ->assertSee('table')
        ->assertSee('ID')
        ->assertSee('thead')
        ->assertSee('tbody')
        ->assertSee('1');
});

it('form > button', function () {
    test()
        ->blade('<x-moonshine::form.button>Click me</x-moonshine::form.button>')
        ->assertSee('button')
        ->assertSee('Click me')
        ->assertSee('btn');
});

it('form > file', function () {
    $file = fake()->filePath();

    test()
        ->blade('<x-moonshine::form.file :files="$files"/>', ['files' => [$file]])
        ->assertSee('input')
        ->assertSee($file);
});

it('form > hint', function () {
    test()
        ->blade('<x-moonshine::form.hint>Hint</x-moonshine::form.hint>')
        ->assertSee('form-hint')
        ->assertSee('Hint');
});

it('form > input', function () {
    test()
        ->blade('<x-moonshine::form.input class="custom-class" />')
        ->assertSee('form-input')
        ->assertSee('input')
        ->assertSee('custom-class');

    test()
        ->blade('<x-moonshine::form.input type="checkbox" class="custom-class" />')
        ->assertSee('form-checkbox')
        ->assertSee('input')
        ->assertSee('custom-class');
});

it('form > input-error', function () {
    test()
        ->blade('<x-moonshine::form.input-error>Error</x-moonshine::form.input-error>')
        ->assertSee('form-error')
        ->assertSee('Error');
});

it('form > input-wrapper', function () {
    test()
        ->withViewErrors([])
        ->blade('<x-moonshine::form.input-wrapper id="testing" label="Im Label">
            <x-moonshine::form.input />
        </x-moonshine::form.input-wrapper>')
        ->assertSee('form-group')
        ->assertSee('Im Label')
        ->assertSee('wrapper_testing')
        ->assertSee('input');
});

it('form > label', function () {
    test()
        ->blade('<x-moonshine::form.label>
            Im Label
        </x-moonshine::form.label>')
        ->assertSee('label')
        ->assertSee('Im Label');
});

it('form > pivot', function () {
    test()
        ->withViewErrors([])
        ->blade('<x-moonshine::form.pivot label="Label" :withFields="true">
            <x-moonshine::form.input name="inner-field" />
        </x-moonshine::form.pivot>')
        ->assertSee('pivotChecker')
        ->assertSee('inner-field');
});
