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

        $wrapperAttributes = $this->field->getWrapperAttributes();

        $wrapperAttributes = $wrapperAttributes->merge([
            'required' => $this->field->getAttribute('required'),
        ]);

        $this->attributes = $wrapperAttributes;
    }

    protected function prepareBeforeRender(): void
    {
        $escapeHint = (bool) $this->getCore()->getConfig()->get('html_escaping.hints', false);

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

            $this->beforeInner = new ComponentSlot((string) $link);
        }

        if ($hint = $this->field->getHint()) {
            $hintRaw = method_exists($this->field, 'isHintRaw')
                ? (bool) \call_user_func([$this->field, 'isHintRaw'])
                : false;

            $this->afterInner = new ComponentSlot(
                $this->getCore()->getRenderer()->render('moonshine::components.form.hint', [
                    'attributes' => new MoonShineComponentAttributeBag(),
                    'slot' => $hint,
                    'escape' => $escapeHint,
                    'raw' => $hintRaw,
                ])->render()
            );
        }
    }

    protected function viewData(): array
    {
        $escapeLabel = (bool) $this->getCore()->getConfig()->get('html_escaping.labels', false);
        $labelRaw = method_exists($this->field, 'isLabelRaw')
            ? (bool) \call_user_func([$this->field, 'isLabelRaw'])
            : false;

        return [
            'label' => $this->field->getLabel(),
            'labelRaw' => $labelRaw,
            'escapeLabel' => $escapeLabel,
            'formName' => $this->field->getFormName(),

            'errors' => data_get($this->field->getErrors(), $this->field->getNameDot()),

            'before' => new ComponentSlot($this->field->getBeforeRender()),
            'after' => new ComponentSlot($this->field->getAfterRender()),
            'slot' => new ComponentSlot(value($this->slot)),

            'beforeInner' => $this->afterInner,
            'afterInner' => $this->beforeInner,

            'isBeforeLabel' => $this->field->isBeforeLabel(),
            'isInsideLabel' => $this->field->isInsideLabel(),
        ];
    }
}
