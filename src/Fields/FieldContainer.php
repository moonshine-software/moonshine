<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\ComponentSlot;
use MoonShine\Components\Link;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Support\MoonShineComponentAttributeBag;

/**
 * @internal
 */
final class FieldContainer extends MoonShineComponent
{
    protected string $view = 'moonshine::components.field-container';

    public ?ComponentSlot $beforeInner = null;

    public ?ComponentSlot $afterInner = null;

    public function __construct(
        public Field $field,
        public View|Closure|string $slot = '',
    ) {
        parent::__construct();

        $this->attributes = $this->field
            ->wrapperAttributes()
            ->merge(['required' => $this->field->attributes()->get('required')]);
    }

    protected function prepareBeforeRender(): void
    {
        if (!$this->field->isPreviewMode() && $this->field->hasLink()) {
            $link = Link::make(
                $this->field->getLinkValue(),
                $this->field->getLinkName(),
            )
                ->customAttributes([
                    'target' => $this->field->isLinkBlank() ? '_blank' : '_self',
                ])
                ->when(
                    $icon = $this->field->getLinkIcon(),
                    fn (Link $link) => $link->icon($icon)
                );

            $this->beforeInner = new ComponentSlot($link);
        }

        if ($hint = $this->field->getHint()) {
            $this->afterInner = new ComponentSlot(
                view('moonshine::components.form.hint', [
                    'attributes' => new MoonShineComponentAttributeBag(),
                    'slot' => $hint,
                ])
            );
        }
    }

    protected function viewData(): array
    {
        return [
            'name' => $this->field->getNameAttribute(),
            'label' => $this->field->getLabel(),
            'formName' => $this->field->getFormName(),

            'before' => new ComponentSlot($this->field->getBeforeRender()),
            'after' => new ComponentSlot($this->field->getAfterRender()),
            'slot' => new ComponentSlot(value($this->slot)),
            'beforeInner' => $this->afterInner,
            'afterInner' => $this->beforeInner,

            'labelBefore' => $this->field->isBeforeLabel(),
            'inLabel' => $this->field->isInLabel(),
        ];
    }
}
