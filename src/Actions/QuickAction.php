<?php

declare(strict_types=1);

namespace MoonShine\Actions;

use Closure;
use Illuminate\Http\RedirectResponse;
use MoonShine\MoonShineUI;
use MoonShine\Traits\WithConfirmation;

/**
 * @method static static make(string $label, Closure $callback, string $message = 'Done')
 */
class QuickAction extends Action
{

    use WithConfirmation;

    public function __construct(
        string $label,
        protected Closure $callback,
        protected string $message = 'Done')
    {
        parent::__construct($label);
    }

    protected ?string $icon = 'heroicons.outline.bolt';

    public function message(): string
    {
        return $this->message;
    }

    public function handle(): RedirectResponse
    {

        call_user_func($this->callback);

        MoonShineUI::toast(
            $this->message,
            'info'
        );

        return back();
    }

}
