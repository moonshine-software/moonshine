<?php

declare(strict_types=1);

namespace MoonShine\Crud\Handlers;

use Closure;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\HasCoreContract;
use MoonShine\Contracts\Core\HasResourceContract;
use MoonShine\Contracts\Core\HasUriKeyContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\HasIconContract;
use MoonShine\Contracts\UI\HasLabelContract;
use MoonShine\Contracts\UI\HasLabelEscapingContract;
use MoonShine\Core\Traits\HasResource;
use MoonShine\Core\Traits\WithCore;
use MoonShine\Core\Traits\WithUriKey;
use MoonShine\Support\Traits\Makeable;
use MoonShine\Support\Traits\WithQueue;
use MoonShine\UI\Enums\DisplayTextType;
use MoonShine\UI\Traits\WithIcon;
use MoonShine\UI\Traits\WithLabel;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method static static make(Closure|string $label)
 *
 * @implements HasResourceContract<CrudResourceContract>
 */
abstract class Handler implements HasIconContract, HasResourceContract, HasUriKeyContract, HasLabelContract, HasCoreContract, HasLabelEscapingContract
{
    use Makeable;
    use WithQueue;
    /**
     * @use HasResource<CrudResourceContract>
     */
    use HasResource;
    use WithIcon;
    use WithUriKey;
    use WithLabel;
    use WithCore;
    use Conditionable;

    /** @var null|Closure(ActionButtonContract, static): ActionButtonContract */
    protected ?Closure $modifyButton = null;

    /** Result of the last prepareButton() call, whose label preference is already final. */
    private ?ActionButtonContract $preparedButton = null;

    /**
     * @var array<array-key, string|int>|(Closure(static): array<array-key, string|int>)
     */
    protected array|Closure $notifyUsers = [];

    public function __construct(Closure|string $label)
    {
        $this->setLabel($label);
    }

    abstract public function handle(): Response;

    abstract public function getButton(): ActionButtonContract;

    public function getUrl(): string
    {
        return $this->getResource()?->getRoute('handler', query: ['handlerUri' => $this->getUriKey()]) ?? '';
    }

    /**
     * @param  Closure(ActionButtonContract $button, static $ctx): ActionButtonContract  $callback
     */
    public function modifyButton(Closure $callback): static
    {
        $this->modifyButton = $callback;

        return $this;
    }

    /**
     * The button with the handler's label preference, also when getButton() skips prepareButton().
     * A button returned by prepareButton() is kept as is, so modifyButton has the last word.
     *
     * @internal
     */
    public function getButtonWithLabelEscaping(): ActionButtonContract
    {
        $button = $this->getButton();

        return $button === $this->preparedButton ? $button : $this->applyLabelEscaping($button);
    }

    /**
     * Passes the handler's label preference to its button unless the button has its own.
     */
    protected function applyLabelEscaping(ActionButtonContract $button): ActionButtonContract
    {
        $escape = $this->getDisplayEscaping(DisplayTextType::LABEL);

        if (! \is_null($escape) && $button instanceof HasLabelEscapingContract && ! $button->hasLabelEscapingOverride()) {
            $button->escapeLabel($escape);
        }

        return $button;
    }

    protected function prepareButton(ActionButtonContract $button): ActionButtonContract
    {
        $button = $this->applyLabelEscaping($button);

        if (! \is_null($this->modifyButton)) {
            $button = \call_user_func($this->modifyButton, $button, $this);
        }

        return $this->preparedButton = $button;
    }

    /**
     * @param array<array-key, string|int>|(Closure(static $ctx): array<array-key, string|int>) $ids
     */
    public function notifyUsers(array|Closure $ids): static
    {
        $this->notifyUsers = $ids;

        return $this;
    }

    /**
     * @return array<array-key, string|int>
     */
    public function getNotifyUsers(): array
    {
        return value($this->notifyUsers, $this);
    }
}
