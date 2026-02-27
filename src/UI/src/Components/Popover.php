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

    public function showOnClick(): self
    {
        return $this->customAttributes([
            'data-trigger' => 'click',
        ]);
    }

    protected function prepareBeforeRender(): void
    {
        $this->customAttributes([
            AlpineJs::eventBlade(JsEvent::POPOVER_TOGGLED, $this->getName()) => 'toggle',
        ]);
    }

    protected function viewData(): array
    {
        return [
            'triggerRaw' => $this->isTriggerRaw(),
            'slot' => $this->getSlot(),
        ];
    }
}
