<?php

use MoonShine\Crud\Components\Fragment;
use MoonShine\UI\Components\FlexibleRender;

it('does not update on load by default', function () {
    $html = (string) Fragment::make()->name('test-fragment')->render();

    expect($html)->not->toContain('DOMContentLoaded', 'setInterval(');
});

it('updates on load independently of polling', function () {
    $html = (string) Fragment::make()
        ->name('test-fragment')
        ->updateOnLoad()
        ->render();

    expect($html)
        ->toContain('DOMContentLoaded', 'fragment_updated:')
        ->not->toContain('setInterval(');
});

it('can disable updating on load', function () {
    $html = (string) Fragment::make()
        ->name('test-fragment')
        ->updateOnLoad()
        ->updateOnLoad(condition: false)
        ->render();

    expect($html)->not->toContain('DOMContentLoaded');
});

it('evaluates the condition with the current fragment at render time', function (bool $enabled) {
    $evaluated = false;
    $fragment = Fragment::make()
        ->updateOnLoad(condition: function (Fragment $fragment) use (&$evaluated): bool {
            $evaluated = true;

            return $fragment->getName() === 'enabled-fragment';
        });

    expect($evaluated)->toBeFalse();

    $fragment->name($enabled ? 'enabled-fragment' : 'disabled-fragment');
    $html = (string) $fragment->render();

    expect($evaluated)->toBeTrue()
        ->and(str_contains($html, 'DOMContentLoaded'))->toBe($enabled);
})->with([true, false]);

it('can combine updating on load with polling', function () {
    $html = (string) Fragment::make()
        ->name('test-fragment')
        ->updateOnLoad()
        ->autoUpdate(ms: 5000)
        ->render();

    expect($html)->toContain('DOMContentLoaded', 'setInterval(', '5000');
});

it('excludes update scripts from the asynchronous fragment response', function () {
    $view = Fragment::make([
        FlexibleRender::make(content: 'Fragment content'),
    ])
        ->name('test-fragment')
        ->updateOnLoad()
        ->autoUpdate(ms: 5000)
        ->render();

    $html = $view->fragment('test-fragment');

    expect($html)
        ->toContain('Fragment content', 'fragmentUpdate(')
        ->not->toContain('<script>', 'DOMContentLoaded', 'setInterval(');
});
