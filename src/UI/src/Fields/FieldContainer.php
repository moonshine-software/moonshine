<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
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

        $this->attributes = new MoonShineComponentAttributeBag($this->field->getWrapperAttributes()->getAttributes())
            ->merge(['required' => $this->field->getAttribute('required')]);
    }

    protected function prepareBeforeRender(): void
    {
        if (! $this->field->isPreviewMode() && $this->field->hasLink()) {
            $link = Link::make(
                $this->field->getLinkValue(),
                $this->field->getLinkName(),
            )
                ->escapeLabel($this->field->isEscapeLinkName())
                ->customAttributes([
                    'target' => $this->field->isLinkBlank() ? '_blank' : '_self',
                ])
                ->when(
                    $icon = $this->field->getLinkIcon(),
                    static fn (Link $link): Link => $link->icon($icon ?? '')
                );

            $this->beforeInner = new ComponentSlot((string) $link);
        }

        if ($this->field->getHint() !== '') {
            $this->afterInner = new ComponentSlot(
                $this->getCore()->getRenderer()->render('moonshine::components.form.hint', [
                    'attributes' => new MoonShineComponentAttributeBag(),
                    'hintHtml' => $this->field->getHintHtml(),
                ])->render()
            );
        }
    }

    private function renderDecoration(Renderable|Htmlable|string $content, bool $escape): ComponentSlot
    {
        return new ComponentSlot(match (true) {
            $content instanceof Htmlable => $content->toHtml(),
            $escape && \is_string($content) => e($content),
            default => $this->stringifySlotContent($content),
        });
    }

    protected function viewData(): array
    {
        return [
            'label' => $this->field->getLabel(),
            'labelHtml' => $this->field->getLabelHtml(),
            'formName' => $this->field->getFormName(),

            'errors' => data_get($this->field->getErrors(), $this->field->getNameDot()),

            'before' => $this->renderDecoration($this->field->getBeforeRender(), $this->field->isEscapeBeforeRender()),
            'after' => $this->renderDecoration($this->field->getAfterRender(), $this->field->isEscapeAfterRender()),
            'slot' => new ComponentSlot($this->stringifySlotContent(value($this->slot))),

            'beforeInner' => $this->afterInner,
            'afterInner' => $this->beforeInner,

            'isBeforeLabel' => $this->field->isBeforeLabel(),
            'isInsideLabel' => $this->field->isInsideLabel(),
        ];
    }

}
