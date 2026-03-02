<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Closure;
use Illuminate\View\ComponentSlot;
use MoonShine\UI\Traits\Components\WithSlotContent;

/**
 * @method static static make(Closure|string $title = '', Closure|array|string $thumbnail = '', Closure|string $url = '#', Closure|array $values = [], Closure|string|null $subtitle = null)
 */
final class Card extends MoonShineComponent
{
    use WithSlotContent;

    protected string $view = 'moonshine::components.card';

    protected Closure|string $header = '';

    protected Closure|string $actions = '';

    protected bool $valuesRaw = false;

    /**
     * @param  (Closure(self):string)|string  $title
     * @param  (Closure(self): string[])|string[]|string  $thumbnail
     * @param  (Closure(self):string)|string  $url
     * @param  (Closure(self): array<string, mixed>)|array<string, mixed>  $values
     * @param  (Closure(self):string)|string|null  $subtitle
     * @param  bool  $overlay
     */
    public function __construct(
        protected Closure|string $title = '',
        protected Closure|array|string $thumbnail = '',
        protected Closure|string $url = '#',
        protected Closure|array $values = [],
        protected Closure|string|null $subtitle = null,
        protected bool $overlay = false,
    ) {
        parent::__construct();
    }

    public function header(Closure|string $value): self
    {
        $this->header = $value;

        return $this;
    }

    public function actions(Closure|string $value): self
    {
        $this->actions = $value;

        return $this;
    }

    public function subtitle(Closure|string $value): self
    {
        $this->subtitle = $value;

        return $this;
    }

    public function url(Closure|string $value): self
    {
        $this->url = $value;

        return $this;
    }

    /**
     * @param  (Closure(self): string[])|string[]|string  $value
     *
     */
    public function thumbnail(Closure|array|string $value): self
    {
        $this->thumbnail = $value;

        return $this;
    }

    /**
     * @param  (Closure(self): array<string, mixed>)|array<string, mixed>  $values
     *
     */
    public function values(Closure|array $values): self
    {
        $this->values = $values;
        $this->valuesRaw = false;

        return $this;
    }

    /**
     * @param  (Closure(self): array<string, mixed>)|array<string, mixed>  $values
     */
    public function valuesHtml(Closure|array $values): self
    {
        $this->values = $values;
        $this->valuesRaw = true;

        return $this;
    }

    public function isValuesRaw(): bool
    {
        return $this->valuesRaw;
    }

    public function overlay(): self
    {
        $this->overlay = true;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        $escapeValues = (bool) $this->getCore()->getConfig()->get('html_escaping.card_values', false);

        return [
            'title' => value($this->title, $this),
            'url' => value($this->url, $this),
            'thumbnail' => value($this->thumbnail, $this),
            'overlay' => $this->overlay,
            'subtitle' => value($this->subtitle, $this),
            'values' => value($this->values, $this),
            'valuesRaw' => $this->isValuesRaw(),
            'escapeValues' => $escapeValues,
            'slot' => $this->getSlot(),
            'header' => new ComponentSlot(
                value($this->header, $this),
            ),
            'actions' => new ComponentSlot(
                value($this->actions, $this),
            ),
        ];
    }
}
