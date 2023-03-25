<?php

declare(strict_types=1);

namespace Leeto\MoonShine\BulkActions;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithIcon;
use Leeto\MoonShine\Traits\WithLabel;

final class BulkAction
{
    use Makeable;
    use WithIcon;
    use WithLabel;

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
