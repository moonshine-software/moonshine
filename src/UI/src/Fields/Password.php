<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Contracts\Hashing\Hasher;
use MoonShine\Support\Enums\TextWrap;

class Password extends Text
{
    protected string $type = 'password';

    protected bool $hasOld = false;

    protected ?TextWrap $textWrap = null;

    protected bool $hashedPassword = true;

    protected function resolvePreview(): string
    {
        return '***';
    }

    protected function resolveValue(): string
    {
        return '';
    }

    protected function hashedPassword(): bool
    {
        return $this->hashedPassword;
    }

    public function isUnescape(): bool
    {
        return true;
    }

    public function raw(): self
    {
        $this->hashedPassword = false;

        return $this;
    }

    protected function resolveOnApply(): ?Closure
    {
        return function ($item) {
            $value = $this->getRequestValue();

            if (\is_string($value) && $value !== '') {
                data_set(
                    $item,
                    $this->getColumn(),
                    $this->hashedPassword()
                        ? $this->getCore()->getContainer(Hasher::class)->make($value)
                        : $value
                );
            }

            return $item;
        };
    }
}
