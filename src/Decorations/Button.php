<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

use Closure;
use MoonShine\Traits\Fields\WithLink;
use MoonShine\Traits\WithIcon;

/**
 * @method static static make(Closure|string $label, string $link, bool $blank = false)
 */
class Button extends Decoration
{
    use WithIcon;
    use WithLink;

    protected string $view = 'moonshine::decorations.button';

    protected string $type = 'primary';

    public function __construct(
        Closure|string $label,
        string $link,
        bool $blank = false
    ) {
        parent::__construct($label);

        $this->link($link, $this->label(), blank: $blank, type: $this->type);
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    protected function type(string $type = 'primary'): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return $this
     */
    public function secondary(): static
    {
        return $this->type('secondary');
    }

    /**
     * @return $this
     */
    public function success(): static
    {
        return $this->type('success');
    }

    /**
     * @return $this
     */
    public function warning(): static
    {
        return $this->type('warning');
    }

    /**
     * @return $this
     */
    public function error(): static
    {
        return $this->type('error');
    }

    /**
     * @return $this
     */
    public function info(): static
    {
        return $this->type('info');
    }
}
