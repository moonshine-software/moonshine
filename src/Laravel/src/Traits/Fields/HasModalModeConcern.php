<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\Collection\ComponentsContract;
use MoonShine\Crud\Components\Fragment;
use MoonShine\Support\Enums\Ability;
use MoonShine\Support\Enums\Action;
use MoonShine\UI\Components\ActionButton;

trait HasModalModeConcern
{
    protected bool $isModalMode = false;

    /** @var (Closure(ActionButtonContract, static): ActionButtonContract)|null */
    protected ?Closure $modifyModalModeButton = null;

    /** @var (Closure(\MoonShine\Contracts\UI\ModalContract): \MoonShine\Contracts\UI\ModalContract)|null */
    protected ?Closure $modifyModalModeModal = null;

    /**
     * @param (Closure(static): (bool|null))|bool|null $condition
     * @param (Closure(ActionButtonContract, static): ActionButtonContract)|null $modifyButton
     * @param (Closure(\MoonShine\Contracts\UI\ModalContract): \MoonShine\Contracts\UI\ModalContract)|null $modifyModal
     */
    public function modalMode(
        Closure|bool|null $condition = null,
        ?Closure $modifyButton = null,
        ?Closure $modifyModal = null
    ): static {
        $this->isModalMode = \is_null($condition) || value($condition, $this);

        $this->modifyModalModeButton = $modifyButton;

        $this->modifyModalModeModal = $modifyModal;

        return $this;
    }

    public function isModalMode(): bool
    {
        return $this->isModalMode;
    }

    public function getModalButton(
        ComponentsContract $components,
        string $label,
        string $fragmentName
    ): ActionButtonContract {
        $button = ActionButton::make($label)->inModal(
            title: $label,
            content: (string) Fragment::make($components)->name($fragmentName),
            name: "modal-{$this->getResourceOrFail()->getUriKey()}-{$this->getRelationName()}",
            builder: $this->modifyModalModeModal ?? static fn (\MoonShine\Contracts\UI\ModalContract $modal): \MoonShine\Contracts\UI\ModalContract => $modal->wide()
        );

        if (! \is_null($this->modifyModalModeButton)) {
            $button = value($this->modifyModalModeButton, $button, $this);
        }

        return $button->canSee(function (mixed $item, ?DataWrapperContract $data): bool {
            if ($data?->getKey() === null) {
                return $this->getResourceOrFail()->hasAction(Action::CREATE)
                       && $this->getResourceOrFail()->can(Ability::CREATE);
            }

            return $this->getResourceOrFail()->hasAction(Action::UPDATE)
               && $item instanceof Model
               && $this->getResourceOrFail()->setItem($item)->can(Ability::UPDATE);
        });
    }
}
