<?php

declare(strict_types=1);

namespace MoonShine\BulkActions;

use Closure;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Actions\MassActionContact;
use MoonShine\Traits\HasCanSee;
use MoonShine\Traits\InDropdownOrLine;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithConfirmation;
use MoonShine\Traits\WithIcon;
use MoonShine\Traits\WithLabel;

final class BulkAction implements MassActionContact
{
    use Makeable;
    use WithIcon;
    use WithLabel;
    use HasCanSee;
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
