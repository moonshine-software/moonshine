<?php

declare(strict_types=1);

namespace MoonShine\ItemActions;

use Closure;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Actions\ItemActionContract;
use MoonShine\Traits\HasCanSee;
use MoonShine\Traits\InDropdownOrLine;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithConfirmation;
use MoonShine\Traits\WithIcon;
use MoonShine\Traits\WithLabel;

/**
 * @method static static make(string $label, Closure $callback, string $message = 'Done')
 */
final class ItemAction implements ItemActionContract
{
    use Makeable;
    use WithIcon;
    use HasCanSee;
    use WithLabel;
    use InDropdownOrLine;
    use WithConfirmation;

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
