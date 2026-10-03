<?php

declare(strict_types=1);

use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\ImportExport\ExportHandler;
use MoonShine\ImportExport\ImportHandler;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

it('preserves handler label preferences in generated buttons', function (string $handlerClass, bool $escape, bool $override): void {
    moonshine()->getConfig()->escapeLabel(! $escape);
    $resource = TestResourceBuilder::new(Item::class);
    $handler = $handlerClass::make('<b>Handler label</b>')
        ->setResource($resource)
        ->escapeLabel($escape);

    if ($override) {
        $handler->modifyButton(static fn (ActionButtonContract $button): ActionButtonContract => $button->escapeLabel(! $escape));
    }

    $document = new DOMDocument();
    @$document->loadHTML((string) $handler->getButton());
    $xpath = new DOMXPath($document);
    $buttons = $xpath->query('//a[contains(@class, "btn")]');
    $expectedEscape = $override ? ! $escape : $escape;

    expect($buttons->length)->toBe(1);
    expect($xpath->query('.//b', $buttons->item(0))->length)->toBe($expectedEscape ? 0 : 1);
    expect(trim($buttons->item(0)->textContent))->toBe($expectedEscape ? '<b>Handler label</b>' : 'Handler label');
})->with([ExportHandler::class, ImportHandler::class])->with([true, false])->with([true, false]);
