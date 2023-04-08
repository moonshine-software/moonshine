<?php

declare(strict_types=1);

namespace Leeto\MoonShine\FormActions;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Leeto\MoonShine\Traits\HasCanSee;
use Leeto\MoonShine\Traits\InDropdownOrLine;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithConfirmation;
use Leeto\MoonShine\Traits\WithIcon;
use Leeto\MoonShine\Traits\WithLabel;

final class FormAction
{
    use Makeable;
    use WithIcon;
    use HasCanSee;
    use WithLabel;
    use InDropdownOrLine;
    use WithConfirmation;

    protected ?string $redirectTo = null;

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

    public function redirectTo(string $redirectTo): self
    {
        $this->redirectTo = $redirectTo;

        return $this;
    }

    public function getRedirectTo(): ?RedirectResponse
    {
        return $this->redirectTo ? redirect($this->redirectTo) : null;
    }
}
