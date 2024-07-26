<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\ComponentSlot;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\UI\Components\Link;
use MoonShine\UI\Components\MoonShineComponent;

/**
 * @internal
 */
final class FieldContainer extends MoonShineComponent
{
    protected string $view = 'moonshine::components.field-container';

    public ?ComponentSlot $beforeInner = null;

    public ?ComponentSlot $afterInner = null;

    public function __construct(
        public FieldContract $field,
        public Renderable|Closure|string $slot = '',
    ) {
        parent::__construct();

        $this->attributes = $this->field
            ->getWrapperAttributes()
            ->merge(['required' => $this->field->getAttributes()->get('required')]);
    }

    protected function prepareBeforeRender(): void
    {
        if (! $this->field->isPreviewMode() && $this->field->hasLink()) {
            $link = Link::make(
                $this->field->getLinkValue(),
                $this->field->getLinkName(),
            )
                ->customAttributes([
                    'target' => $this->field->isLinkBlank() ? '_blank' : '_self',
                ])
                ->when(
                    $icon = $this->field->getLinkIcon(),
                    static fn (Link $link): Link => $link->icon($icon)
                );

            $this->beforeInner = new ComponentSlot($link);
        }

        if ($hint = $this->field->getHint()) {
            $this->afterInner = new ComponentSlot(
                $this->getCore()->getRenderer()->render('moonshine::components.form.hint', [
                    'attributes' => new MoonShineComponentAttributeBag(),
                    'slot' => $hint,
                ])
            );
        }
    }

    protected function viewData(): array
    {
        return [
            'errors' => data_get($this->field->getErrors(), $this->field->getNameDot()),
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
