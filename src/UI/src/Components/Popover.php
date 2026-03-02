<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Traits\Components\WithSlotContent;

/** @method static static make(string $title, string $trigger, string $placement = 'right') */
final class Popover extends MoonShineComponent
{
    use WithSlotContent;

    protected string $view = 'moonshine::components.popover';

    protected bool $triggerRaw = false;

    public function __construct(
        public string $title,
        public string $trigger = '',
        public string $placement = 'right',
    ) {
        parent::__construct();

        if ($this->trigger === '') {
            $this->trigger = $title;
        }
    }

    public function showOnClick(): self
    {
        return $this->customAttributes([
            'data-trigger' => 'click',
        ]);
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function trigger(string $trigger): self
    {
        $this->trigger = $trigger;
        $this->triggerRaw = false;

        return $this;
    }

    public function triggerHtml(string $trigger): self
    {
        $this->trigger = $trigger;
        $this->triggerRaw = true;

        return $this;
    }

    public function isTriggerRaw(): bool
    {
        return $this->triggerRaw;
    }

    protected function prepareBeforeRender(): void
    {
        $this->customAttributes([
            AlpineJs::eventBlade(JsEvent::POPOVER_TOGGLED, $this->getName()) => 'toggle',
        ]);
    }

    protected function viewData(): array
    {
        $escapeUi = (bool) $this->getCore()->getConfig()->get('html_escaping.ui_elements', false);

        return [
            'slot' => $this->getSlot(),
            'escapeUi' => $escapeUi,
            'triggerRaw' => $this->isTriggerRaw(),
        ];
    }
}
