<?php

declare(strict_types=1);

namespace MoonShine\Actions;

use Closure;
use Illuminate\Http\RedirectResponse;

/**
 * @method static static make(string $label = '', Closure $callback = null)
 */
class QuickAction extends Action
{
    protected Closure $callback;

    public function __construct(string $label = '', Closure $callback = null)
    {

        if (is_null($callback)) {
            throw new \InvalidArgumentException('Invalid callback');
        }

        $this->callback = $callback;

        parent::__construct($label);
    }

    protected ?string $icon = 'heroicons.outline.bolt';

    public function handle(): RedirectResponse
    {
        call_user_func($this->callback);

        return back();
    }

}
