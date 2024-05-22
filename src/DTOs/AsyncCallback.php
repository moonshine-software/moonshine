<?php

declare(strict_types=1);

namespace MoonShine\DTOs;

use Illuminate\Contracts\Support\Arrayable;

final readonly class AsyncCallback implements Arrayable
{
    public function __construct(
        private ?string $success,
        private ?string $before,
    ) {
    }

    public static function with(?string $success = null, ?string $before = null): self
    {
        return new self($success, $before);
    }

    public function getBefore(): ?string
    {
        return $this->before;
    }

    public function getSuccess(): ?string
    {
        return $this->success;
    }

    public function toArray(): array
    {
        return [
            'before' => $this->getBefore(),
            'success' => $this->getSuccess(),
        ];
    }
}
