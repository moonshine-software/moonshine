<?php

declare(strict_types=1);

namespace MoonShine\BulkActions;

use Closure;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Actions\MassActionContract;
use MoonShine\Traits\HasCanSee;
use MoonShine\Traits\InDropdownOrLine;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithConfirmation;
use MoonShine\Traits\WithErrorMessage;
use MoonShine\Traits\WithIcon;
use MoonShine\Traits\WithLabel;

/**
 * @method static static make(string $label, Closure $callback, string $message = 'Done')
 */
final class BulkAction implements MassActionContract
{
    use Makeable;
    use WithIcon;
    use WithLabel;
    use HasCanSee;
    use InDropdownOrLine;
    use WithConfirmation;
    use WithErrorMessage;

    public function __construct(
        string $label,
        protected Closure $callback,
        protected string $message = 'Done',
    ) {
        $this->setLabel($label);
        $this->errorMessage = __('moonshine::ui.saved_error');
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
