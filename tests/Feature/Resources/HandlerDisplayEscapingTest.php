<?php

declare(strict_types=1);

use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Crud\Handlers\Handler;
use MoonShine\ImportExport\ExportHandler;
use MoonShine\ImportExport\ImportHandler;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Components\ActionButton;
use Symfony\Component\HttpFoundation\Response;

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

it('keeps explicit preferences of buttons prepared by custom handlers', function (?bool $handlerEscape, bool $buttonEscape, bool $expectedEscape): void {
    $handler = new class ('<b>Custom</b>') extends Handler {
        public bool $buttonEscape = true;

        public function handle(): Response
        {
            return new Response();
        }

        public function getButton(): ActionButtonContract
        {
            return $this->prepareButton(
                ActionButton::make($this->getLabel())->escapeLabel($this->buttonEscape)
            );
        }
    };
    $handler->buttonEscape = $buttonEscape;

    if ($handlerEscape !== null) {
        $handler->escapeLabel($handlerEscape);
    }

    expect((string) $handler->getButton())
        ->toContain($expectedEscape ? '&lt;b&gt;Custom&lt;/b&gt;' : '<b>Custom</b>');
})->with([
    'button opt out without handler preference' => [null, false, false],
    'button opt in without handler preference' => [null, true, true],
    'explicit handler preference' => [true, false, true],
]);
