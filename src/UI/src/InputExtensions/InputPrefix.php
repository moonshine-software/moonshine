<?php

declare(strict_types=1);

namespace MoonShine\UI\InputExtensions;

final class InputPrefix extends InputExtension
{
    protected ?bool $escape = null;

    public function escape(bool $escape = true): self
    {
        $this->escape = $escape;

        return $this;
    }

    protected function systemViewData(): array
    {
        $escape = $this->escape ?? $this->getCore()->getConfig()->isEscapePrefix();

        return [
            ...parent::systemViewData(),
            'escapePrefix' => $escape,
        ];
    }

    public function __construct(string $content)
    {
        parent::__construct($content);
        $this->customView('moonshine::form.input-extensions.prefix');
    }
}
