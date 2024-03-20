<?php

declare(strict_types=1);

namespace MoonShine\Table;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\ComponentAttributeBag;
use MoonShine\ActionButtons\ActionButtons;
use MoonShine\Components\TableBuilder;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Fields\Fields;
use MoonShine\Fields\ID;
use MoonShine\Traits\Makeable;
use Throwable;

final class TableRow implements MoonShineRenderable
{
    use Makeable;

    protected bool $hasActions = false;

    protected bool $vertical = false;

    protected bool $editable = false;

    protected bool $preview = false;

    protected bool $simple = false;

    protected ?int $index = null;

    protected bool $hasClickAction = false;

    public function __construct(
        protected mixed $data,
        protected Fields $fields,
        protected ActionButtons $actions,
        protected ?Closure $trAttributes = null,
        protected ?Closure $tdAttributes = null,
        protected ?Closure $systemTrAttributes = null,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function getKey(): int|string
    {
        return $this->getFields()
            ->findByClass(ID::class)
            ?->value() ?? '';
    }

    public function setIndex(int $value = 0): self
    {
        $this->index = $value;

        return $this;
    }

    public function getIndex(int $default = 0): int
    {
        return $this->index ?? $default;
    }

    /**
     * @throws Throwable
     */
    public function hasKey(): bool
    {
        return $this->getKey() !== 0 && $this->getKey() !== '';
    }

    public function getFields(): Fields
    {
        return $this->fields;
    }

    public function getActions(): ActionButtons
    {
        return $this->actions;
    }

    public function trAttributes(int $row): ComponentAttributeBag
    {
        $row = $this->getIndex($row);

        $attributes = new ComponentAttributeBag();

        if (is_null($this->trAttributes)) {
            $this->trAttributes = static fn(): ComponentAttributeBag => new ComponentAttributeBag();
        }

        $attributes = value($this->trAttributes, $this->data, $row, $attributes, $this);

        if (! is_null($this->systemTrAttributes)) {
            return value($this->systemTrAttributes, $this->data, $row, $attributes, $this);
        }

        return $attributes;
    }

    public function tdAttributes(int $row, int $cell): ComponentAttributeBag
    {
        $row = $this->getIndex($row);

        $attributes = new ComponentAttributeBag();

        if (is_null($this->tdAttributes)) {
            $this->tdAttributes = static fn(): ComponentAttributeBag => new ComponentAttributeBag();
        }

        return value($this->tdAttributes, $this->data, $row, $cell, $attributes, $this);
    }

    public function mapTableStates(TableBuilder $table): self
    {
        $this->tdAttributes = $table->getTdAttributes();
        $this->trAttributes = $table->getTrAttributes();
        $this->systemTrAttributes = $table->getSystemTrAttributes();
        $this->hasActions = $table->getBulkButtons()->isNotEmpty();
        $this->vertical = $table->isVertical();
        $this->editable = $table->isEditable();
        $this->preview = $table->isPreview();
        $this->simple = $table->isSimple();
        $this->hasClickAction = $table->attributes()->get('data-click-action') !== null;

        return $this;
    }

    public function render(): View
    {
        return view('moonshine::components.table.body', [
            'rows' => [
                $this
            ],
            'hasActions' => $this->hasActions,
            'vertical' => $this->vertical,
            'editable' => $this->editable,
            'preview' => $this->preview,
            'simple' => $this->simple,
            'hasClickAction' => $this->hasClickAction,
        ]);
    }

    public function __toString(): string
    {
        return (string) $this->render();
    }

    public function toArray(): array
    {
        return (array) $this->data;
    }
}
