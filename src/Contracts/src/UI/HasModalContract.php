<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;
use MoonShine\Support\Enums\HttpMethod;

/**
 * @template-covariant T of ComponentContract
 */
interface HasModalContract
{
    public function isInModal(): bool;

    /**
     * @return ?T
     */
    public function getModal(): ?ComponentContract;

    public function toggleModal(string $name = 'default'): static;

    public function openModal(): static;

    public function inModal(
        Closure|string|null $title = null,
        Closure|string|null $content = null,
        Closure|string|null $name = null,
        ?Closure $builder = null,
    ): static;

    public function withConfirm(
        Closure|string|null $title = null,
        Closure|string|null $content = null,
        Closure|string|null $button = null,
        Closure|array|null $fields = null,
        HttpMethod $method = HttpMethod::POST,
        ?Closure $formBuilder = null,
        ?Closure $modalBuilder = null,
        Closure|string|null $name = null,
    ): static;
}
