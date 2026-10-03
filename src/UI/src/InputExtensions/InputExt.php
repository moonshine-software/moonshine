<?php

declare(strict_types=1);

namespace MoonShine\UI\InputExtensions;

final class InputExt extends InputExtension
{
    protected ?bool $escape = null;

    public function escape(bool $escape = true): self
    {
        $this->escape = $escape;

        return $this;
    }

    protected function systemViewData(): array
    {
        $escape = $this->escape ?? $this->getCore()->getConfig()->isEscapeSuffix();

        return [
            ...parent::systemViewData(),
            'escapeSuffix' => $escape,
        ];
    }

    protected string $view = 'moonshine::form.input-extensions.ext';
}
