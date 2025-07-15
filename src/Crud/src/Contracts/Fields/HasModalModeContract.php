<?php

declare(strict_types=1);

namespace MoonShine\Crud\Contracts\Fields;

use Closure;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Core\Collections\Components;
use MoonShine\UI\Components\Modal;

interface HasModalModeContract
{
    /**
     * @param (Closure(static $ctx): bool)|bool|null  $condition
     * @param (Closure(ActionButtonContract $button, static $ctx): ActionButtonContract)|null  $modifyButton
     * @param (Closure(Modal $modal, ActionButtonContract $ctx): Modal)|null  $modifyModal
     */
    public function modalMode(
        Closure|bool|null $condition = null,
        ?Closure $modifyButton = null,
        ?Closure $modifyModal = null
    ): static;

    public function isModalMode(): bool;

    public function getModalButton(
        Components $components,
        string $label,
        string $fragmentName
    ): ActionButtonContract;
}
