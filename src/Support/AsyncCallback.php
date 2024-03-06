<?php

declare(strict_types=1);

namespace MoonShine\Support;

final class AsyncCallback
{
    public function __construct(
        private readonly ?string $success,
        private readonly ?string $before,
    ) {

    }

    public static function with(?string $success = null, ?string $before = null): AsyncCallback
    {
        return new AsyncCallback($success, $before);
    }

    public function before(): ?string
    {
        return $this->before;
    }

    public function success(): ?string
    {
        return $this->success;
    }
}