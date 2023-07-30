<?php

namespace MoonShine\Actions;

use Closure;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Traits\HasCanSee;
use MoonShine\Traits\InDropdownOrLine;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithErrorMessage;
use MoonShine\Traits\WithIcon;
use MoonShine\Traits\WithLabel;
use MoonShine\Traits\WithModal;

/**
 * @method static static make(string $label, Closure $callback, string $message = 'Done')
 */
abstract class ResourceAction
{
    use Makeable;
    use WithIcon;
    use WithLabel;
    use HasCanSee;
    use InDropdownOrLine;
    use WithModal;
    use WithErrorMessage;

    public function __construct(
        string $label,
        protected Closure $callback,
        protected string $message = 'Done',
    ) {
        $this->setLabel($label);
    }

    public function message(): string
    {
        return $this->message;
    }

    public function callback(Model $model): mixed
    {
        return call_user_func($this->callback, $model);
    }
}
