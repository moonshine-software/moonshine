<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use MoonShine\Support\DTOs\Select\Option;
use MoonShine\Support\DTOs\Select\OptionGroup;
use MoonShine\Support\DTOs\Select\Options;

uses()->group('fields');
uses()->group('selects');

it('escapes select option labels and values once at the html boundary', function (): void {
    $label = 'Sea "Wolves" & <Waves>';
    $value = 'team"1&2';
    $option = new Option($label, $value, selected: true);
    $group = new OptionGroup(
        'Group <Name>',
        new Options([new Option('Nested "Wolves"', 'nested&1')]),
    );

    $html = Blade::render(
        '<x-moonshine::form.select :values="$values" />',
        ['values' => [$group->toArray(), $option->toArray()]],
    );

    expect($option->getLabel())->toBe($label);
    expect($option->getValue())->toBe($value);
    expect($html)
        ->toContain('value="team&quot;1&amp;2"')
        ->toContain('Sea &quot;Wolves&quot; &amp; &lt;Waves&gt;')
        ->toContain('label="Group &lt;Name&gt;"')
        ->toContain('Nested &quot;Wolves&quot;')
        ->toContain('selected');
    expect($html)->not->toContain('&amp;quot;');
});
