<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

use Leeto\MoonShine\Helpers\Condition;

trait ShowOrHide
{
    protected bool $showOnIndex = true;

    protected bool $showOnExport = false;

    protected bool $showOnForm = true;

    protected bool $showOnDetail = true;

    /**
     * Set field as visible on index page, based on condition
     *
     * @param  mixed  $condition
     * @return $this
     */
    public function showOnIndex(mixed $condition = null): static
    {
        $this->showOnIndex = Condition::boolean($condition, true);

        return $this;
    }

    /**
     * Set field as hidden on index page, based on condition
     *
     * @param  mixed  $condition
     * @return $this
     */
    public function hideOnIndex(mixed $condition = null): static
    {
        $this->showOnIndex = Condition::boolean($condition, false);

        return $this;
    }

    /**
     * Set field as visible on create/edit page, based on condition
     *
     * @param  mixed  $condition
     * @return $this
     */
    public function showOnForm(mixed $condition = null): static
    {
        $this->showOnForm = Condition::boolean($condition, true);

        return $this;
    }

    /**
     * Set field as hidden on create/edit page, based on condition
     *
     * @param  mixed  $condition
     * @return $this
     */
    public function hideOnForm(mixed $condition = null): static
    {
        $this->showOnForm = Condition::boolean($condition, false);

        return $this;
    }

    /**
     * Set field as visible on show page, based on condition
     *
     * @param  mixed  $condition
     * @return $this
     */
    public function showOnDetail(mixed $condition = null): static
    {
        $this->showOnDetail = Condition::boolean($condition, true);

        return $this;
    }

    /**
     * Set field as hidden on show page, based on condition
     *
     * @param  mixed  $condition
     * @return $this
     */
    public function hideOnDetail(mixed $condition = null): static
    {
        $this->showOnDetail = Condition::boolean($condition, false);

        return $this;
    }

    /**
     * Set field as visible in export report, based on condition
     *
     * @param  mixed  $condition
     * @return $this
     */
    public function showOnExport(mixed $condition = null): static
    {
        $this->showOnExport = Condition::boolean($condition, true);

        return $this;
    }

    /**
     * Set field as hidden in export report, based on condition
     *
     * @param  mixed  $condition
     * @return $this
     */
    public function hideOnExport(mixed $condition = null): static
    {
        $this->showOnExport = Condition::boolean($condition, false);

        return $this;
    }

    public function isOnIndex(): bool
    {
        return $this->showOnIndex;
    }

    public function isOnForm(): bool
    {
        return $this->showOnForm;
    }

    public function isOnExport(): bool
    {
        return $this->showOnExport;
    }

    public function isOnDetail(): bool
    {
        return $this->showOnDetail;
    }
}
