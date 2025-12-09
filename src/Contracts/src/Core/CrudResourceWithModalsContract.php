<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use MoonShine\Contracts\UI\ModalContract;

/**
 * @internal
 */
interface CrudResourceWithModalsContract
{
    public function isCreateInModal(): bool;

    public function isEditInModal(): bool;

    public function isDetailInModal(): bool;

    public function resolveCreateModal(ModalContract $modal): ModalContract;

    public function resolveEditModal(ModalContract $modal): ModalContract;

    public function resolveDetailModal(ModalContract $modal): ModalContract;

    public function resolveDeleteModal(ModalContract $modal): ModalContract;

    public function resolveMassDeleteModal(ModalContract $modal): ModalContract;
}
