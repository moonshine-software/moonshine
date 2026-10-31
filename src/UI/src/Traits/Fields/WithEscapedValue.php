<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Closure;
use MoonShine\Contracts\Core\DependencyInjection\ValueEscapingConfiguratorContract;

trait WithEscapedValue
{
    protected ?bool $unescape = null;

    /**
     * @var (Closure(static): bool)|null
     */
    protected ?Closure $escapeOnApply = null;

    public function escape(): static
    {
        $this->unescape = false;

        return $this;
    }

    public function unescape(): static
    {
        $this->unescape = true;

        return $this;
    }

    public function isUnescape(): bool
    {
        if ($this->unescape !== null) {
            return $this->unescape;
        }

        $config = $this->getCore()->getConfig();

        // Configurators without value escaping settings keep the safe default.
        return $config instanceof ValueEscapingConfiguratorContract && ! $config->isEscapeValue();
    }

    /**
     * @param (Closure(static $ctx): bool)|null $condition = null
     */
    public function escapeOnApply(?Closure $condition = null): static
    {
        $this->escapeOnApply = $condition ?? static fn (): bool => true;

        return $this;
    }

    public function isUnescapeOnApply(): bool
    {
        if ($this->escapeOnApply === null) {
            $config = $this->getCore()->getConfig();

            return $config instanceof ValueEscapingConfiguratorContract && ! $config->isEscapeValueOnApply();
        }

        return ! \call_user_func($this->escapeOnApply, $this);
    }

    /**
     * Fields with this trait escape their own preview, so link names get it decoded
     * and are escaped once afterwards instead of twice.
     */
    protected function getLinkNamePreview(string $preview): string
    {
        return $this->isUnescape() ? $preview : htmlspecialchars_decode($preview, ENT_QUOTES);
    }

    protected function escapeValue(?string $value = null, bool $doubleEncode = true): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
    }
}
