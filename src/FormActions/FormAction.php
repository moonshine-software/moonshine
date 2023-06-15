<?php

declare(strict_types=1);

namespace MoonShine\FormActions;

use Illuminate\Http\RedirectResponse;
use MoonShine\Actions\ResourceAction;
use MoonShine\Contracts\Actions\ItemActionContract;

final class FormAction extends ResourceAction implements ItemActionContract
{
    protected ?string $redirectTo = null;

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
