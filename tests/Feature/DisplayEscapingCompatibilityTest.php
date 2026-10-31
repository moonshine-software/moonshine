<?php

declare(strict_types=1);

use MoonShine\Contracts\Core\DependencyInjection\ConfiguratorContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\HasHintContract;
use MoonShine\Contracts\UI\HasLabelContract;
use MoonShine\Contracts\UI\HasLinkContract;
use MoonShine\Contracts\UI\ModalContract;
use MoonShine\Contracts\UI\OffCanvasContract;
use MoonShine\Crud\Handlers\Handler;
use MoonShine\Crud\Handlers\Handlers;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\CardsBuilder;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Modal;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Text;
use Symfony\Component\HttpFoundation\Response;

it('keeps existing contracts free of escaping methods', function (string $contract, array $methods): void {
    $reflection = new ReflectionClass($contract);

    foreach ($methods as $method) {
        expect($reflection->hasMethod($method))->toBeFalse();
    }
})->with([
    'configurator' => [ConfiguratorContract::class, ['isEscapeLabel', 'isEscapeHint', 'isEscapePrefix', 'isEscapeSuffix', 'isEscapeBeforeRender', 'isEscapeAfterRender']],
    'label' => [HasLabelContract::class, ['escapeLabel', 'unescapeLabel', 'isEscapeLabel', 'hasLabelEscapingOverride', 'getLabelHtml']],
    'hint' => [HasHintContract::class, ['escapeHint', 'unescapeHint', 'isEscapeHint', 'getHintHtml']],
    'link' => [HasLinkContract::class, ['escapeLinkName', 'unescapeLinkName', 'isEscapeLinkName']],
    'modal' => [ModalContract::class, ['escapeTitle', 'unescapeTitle', 'isEscapeTitle']],
    'off-canvas' => [OffCanvasContract::class, ['escapeTitle', 'unescapeTitle', 'isEscapeTitle']],
    'field' => [FieldContract::class, ['escapeBeforeRender', 'unescapeBeforeRender', 'isEscapeBeforeRender', 'escapeAfterRender', 'unescapeAfterRender', 'isEscapeAfterRender']],
]);

it('passes the prepared preview to link name callbacks', function (): void {
    $select = Select::make('Status', 'status')
        ->options([1 => 'Active'])
        ->setValue(1)
        ->link('/status', name: static fn (string $value): string => "Go to {$value}");

    $date = Date::make('Date', 'date')
        ->format('d.m.Y')
        ->setValue('2026-10-20 00:00:00')
        ->link('/date', name: static fn (string $value): string => "On {$value}");

    expect((string) $select->preview())->toContain('Go to Active')->not->toContain('Go to 1');
    expect((string) $date->preview())->toContain('On 20.10.2026');
});

it('escapes markup from link name callbacks built from the preview', function (): void {
    $field = Select::make('Status', 'status')
        ->options([1 => '<b>Active</b>'])
        ->setValue(1)
        ->link('/status', name: static fn (string $value): string => "Go to {$value}");

    expect((string) $field->preview())->toContain('Go to &lt;b&gt;Active&lt;/b&gt;')->not->toContain('<b>Active</b>');
});

it('rebuilds prepared labels replaced through custom view data', function (bool $escape): void {
    $button = ActionButton::make('Edit')
        ->escapeLabel($escape)
        ->customView('moonshine::components.action-button', ['label' => '<b>Change</b>']);
    $box = Box::make('Edit', [])
        ->escapeLabel($escape)
        ->customView('moonshine::components.layout.box', ['label' => '<b>Change</b>']);

    foreach ([(string) $button, (string) $box] as $html) {
        expect($html)
            ->toContain($escape ? '&lt;b&gt;Change&lt;/b&gt;' : '<b>Change</b>')
            ->not->toContain('Edit');
    }
})->with([true, false]);

it('rebuilds prepared titles replaced through custom view data', function (): void {
    $modal = Modal::make('Old title')->customView('moonshine::components.modal', ['title' => '<b>New title</b>']);

    expect((string) $modal)->toContain('&lt;b&gt;New title&lt;/b&gt;')->not->toContain('Old title');
});

it('applies handler label preferences to buttons that skip prepareButton', function (bool $escape): void {
    moonshine()->getConfig()->escapeLabel(! $escape);

    $handler = new class ('<b>Run</b>') extends Handler {
        public function handle(): Response
        {
            return new Response();
        }

        public function getButton(): ActionButtonContract
        {
            return ActionButton::make($this->getLabel(), $this->getUrl());
        }
    };

    $handler->setResource(TestResourceBuilder::new(Item::class))->escapeLabel($escape);

    expect((string) Handlers::make([$handler])->getButtons()->first())
        ->toContain($escape ? '&lt;b&gt;Run&lt;/b&gt;' : '<b>Run</b>');
})->with([true, false]);

it('generates handlers that prepare their buttons', function (): void {
    expect(file_get_contents(\dirname(__DIR__, 2) . '/src/Laravel/stubs/Handler.stub'))
        ->toContain('return $this->prepareButton(');
});

it('renders tabs whose data has no prepared label', function (): void {
    $tab = new class ('<b>Tab</b>', [Box::make('Content', [])]) extends Tab {
        protected function viewData(): array
        {
            return array_diff_key(parent::viewData(), ['labelHtml' => true]);
        }
    };

    expect((string) Tabs::make([$tab]))->toContain('&lt;b&gt;Tab&lt;/b&gt;');
});

it('resolves card value label preferences per item', function (): void {
    moonshine()->getConfig()->escapeLabel(true);

    $cards = CardsBuilder::make([['title' => '<b>First</b>'], ['title' => '<i>Second</i>']], [
        Text::make(static fn (Text $field): string => (string) $field->toValue(), 'title')->unescapeLabel(),
    ]);

    expect((string) $cards)->toContain('<b>First</b>:', '<i>Second</i>:');
});

it('keeps duplicate card value labels consistent with their values', function (): void {
    moonshine()->getConfig()->escapeLabel(true);

    $cards = CardsBuilder::make([['first' => 'A', 'second' => 'B']], [
        Text::make('<b>Name</b>', 'first'),
        Text::make('<b>Name</b>', 'second')->unescapeLabel(),
    ]);

    // Values are keyed by label, so the last field wins for both the value and its label preference.
    expect((string) $cards)->toContain('<b>Name</b>:')->not->toContain('&lt;b&gt;Name');
});
