<?php

use MoonShine\Fields\Text;
use MoonShine\Models\MoonshineUser;
use MoonShine\MoonShine;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('components');

it('menu', function () {
    MoonShine::menu([
        TestResourceBuilder::new()
            ->setTestTitle('Testing menu item'),
    ]);

    test()
        ->blade('<x-moonshine::menu-component />')
        ->assertSee('Testing menu item');
});

it('async modal', function () {
    test()
        ->blade(
            '<x-moonshine::async-modal id="async" route="/">
            OuterHtml
        </x-moonshine::async-modal>'
        )
        ->assertSee('OuterHtml');
});

it('form and input', function () {
    test()
        ->blade(
            '<x-moonshine::form>
            <x-moonshine::form.input name="test-input" />
        </x-moonshine::form>'
        )
        ->assertSee('form')
        ->assertSee('input')
        ->assertSee('test-input');
});

it('table with slots', function () {
    test()
        ->blade(
            '<x-moonshine::table>
            <x-slot:thead>
                <th>ID</th>
            </x-slot:thead>
            <x-slot:tbody>
                <tr><td>1</td></tr>
            </x-slot:tbody>
            <x-slot:tfoot>
            <tr><td>footer</td></tr>
            </x-slot:tfoot>
        </x-moonshine::table>'
        )
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
            'values' => [['id' => 1]],
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
        ->blade(
            '<x-moonshine::form.input-wrapper id="testing" label="Im Label">
            <x-moonshine::form.input />
        </x-moonshine::form.input-wrapper>'
        )
        ->assertSee('form-group')
        ->assertSee('Im Label')
        ->assertSee('wrapper_testing')
        ->assertSee('input');
});

it('form > label', function () {
    test()
        ->blade(
            '<x-moonshine::form.label>
            Im Label
        </x-moonshine::form.label>'
        )
        ->assertSee('label')
        ->assertSee('Im Label');
});

it('form > pivot', function () {
    test()
        ->withViewErrors([])
        ->blade(
            '<x-moonshine::form.pivot label="Label" :withFields="true">
            <x-moonshine::form.input name="inner-field" />
        </x-moonshine::form.pivot>'
        )
        ->assertSee('pivotChecker')
        ->assertSee('inner-field');
});


it('form > range', function () {
    test()
        ->withViewErrors([])
        ->blade(
            '<x-moonshine::form.range
            fromName="from"
            toName="to"
            fromValue="12000"
            toValue="29000"
        />'
        )
        ->assertSee('form-group-range')
        ->assertSee('from')
        ->assertSee('to')
        ->assertSee('12000')
        ->assertSee('29000');
});

it('form > select', function () {
    test()
        ->withViewErrors([])
        ->blade(
            '<x-moonshine::form.select
            :values="$values"
        />',
            ['values' => [1 => 'Option 1', 2 => 'Option 2']]
        )
        ->assertSeeInOrder(['Option 1', 'Option 2']);
});

it('form > switcher', function () {
    test()
        ->withViewErrors([])
        ->blade('<x-moonshine::form.switcher />')
        ->assertSee('form-switcher');
});

it('form > textarea', function () {
    test()
        ->withViewErrors([])
        ->blade('<x-moonshine::form.textarea>Editor value</x-moonshine::form.textarea>')
        ->assertSee('Editor value');
});

it('badge', function () {
    test()
        ->blade('<x-moonshine::badge color="purple">Badge value</x-moonshine::badge>')
        ->assertSee('badge badge-purple')
        ->assertSee('Badge value');
});

it('box', function () {
    test()
        ->blade('<x-moonshine::box title="Box title">Box value</x-moonshine::box>')
        ->assertSee('box')
        ->assertSee('Box title')
        ->assertSee('Box value');
});

it('column', function () {
    test()
        ->blade('<x-moonshine::column colSpan="6" adaptiveColSpan="6">Column value</x-moonshine::column>')
        ->assertSee('col-span-6')
        ->assertSee('xl:col-span-6')
        ->assertSee('Column value');
});

it('grid', function () {
    test()
        ->blade('<x-moonshine::grid>Grid value</x-moonshine::grid>')
        ->assertSee('Grid value');
});

it('dropdown', function () {
    test()
        ->blade(
            '<x-moonshine::dropdown
            title="Dropdown title"
        >
            Dropdown value
            <x-slot:toggler>Click me</x-slot:toggler>
            <x-slot:footer>Dropdown footer</x-slot:footer>
        </x-moonshine::dropdown>'
        )
        ->assertSee('Dropdown title')
        ->assertSee('Click me')
        ->assertSee('Dropdown footer')
        ->assertSee('Dropdown value');
});


it('field-container', function () {
    $field = Text::make('Email');
    $resource = TestResourceBuilder::new(MoonshineUser::class)
        ->setTestFields([$field]);
    $item = MoonshineUser::factory()->create();

    test()
        ->withViewErrors(['email' => 'Email is required'])
        ->blade(
            '<x-moonshine::field-container
            :resource="$resource"
            :field="$field"
            :item="$item"
        ></x-moonshine::field-container>',
            [
                'resource' => $resource,
                'field' => $field,
                'item' => $item,
            ]
        )
        ->assertSee('Email')
        ->assertSee('Email is required');
});

it('icon', function () {
    test()
        ->blade(
            '<x-moonshine::icon size="6"
            color="purple"
            icon="heroicons.outline.pencil"
        />'
        )
        ->assertSee('w-6')
        ->assertSee('text-purple')
        ->assertSee('svg');
});

it('link', function () {
    test()
        ->blade(
            '<x-moonshine::link
            icon="heroicons.outline.pencil"
        >Link</x-moonshine::link>'
        )
        ->assertSee('Link')
        ->assertSee('svg');
});

it('link-native', function () {
    test()
        ->blade(
            '<x-moonshine::link-native
            icon="heroicons.outline.pencil"
        >Link</x-moonshine::link-native>'
        )
        ->assertSee('Link')
        ->assertSee('svg');
});

it('loader', function () {
    test()
        ->blade('<x-moonshine::loader />')
        ->assertSee('svg');
});

it('offcanvas', function () {
    test()
        ->blade(
            '<x-moonshine::offcanvas title="Offcanvas title">
            Offcanvas value
            <x-slot:toggler>Click me</x-slot:toggler>
        </x-moonshine::offcanvas>'
        )
        ->assertSee('Offcanvas title')
        ->assertSee('Offcanvas value')
        ->assertSee('Click me');
});

it('thumbnails', function () {
    $value = fake()->filePath();

    test()
        ->blade('<x-moonshine::thumbnails :value="$value"/>', ['value' => $value])
        ->assertSee($value)
        ->assertSee('img');
});
