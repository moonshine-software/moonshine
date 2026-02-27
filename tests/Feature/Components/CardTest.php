<?php

declare(strict_types=1);

use MoonShine\UI\Components\Card;

test('card values escapes html by default', function () {
    $card = Card::make('Test Card')
        ->values([
            'Name' => 'John <script>alert("xss")</script>',
            'Email' => 'test@example.com <img src=x onerror=alert("xss")>',
        ]);

    expect($card->isValuesRaw())->toBeFalse();
});

test('card valuesHtml renders raw html', function () {
    $card = Card::make('Test Card')
        ->valuesHtml([
            'Name' => 'John <strong>Doe</strong>',
            'Email' => '<em>test@example.com</em>',
        ]);

    expect($card->isValuesRaw())->toBeTrue();
});

test('card values resets raw flag when called after valuesHtml', function () {
    $card = Card::make('Test Card')
        ->valuesHtml(['Name' => '<strong>Raw</strong>'])
        ->values(['Name' => 'Safe']);

    expect($card->isValuesRaw())->toBeFalse();
});
