<?php

declare(strict_types=1);

namespace MoonShine\Laravel\DependencyInjection;

use Countable;
use Illuminate\Contracts\Translation\Translator as IlluminateTranslator;
use MoonShine\Contracts\Core\DependencyInjection\TranslatorContract;
use UnexpectedValueException;

final readonly class Translator implements TranslatorContract
{
    public function __construct(private IlluminateTranslator $translator)
    {
    }

    public function all(?string $locale = null): array
    {
        /** @var array<string, mixed>|null $translates */
        $translates = $this->get('moonshine::ui', locale: $locale);

        return \is_array($translates) ? $translates : [];
    }

    public function get(string $key, array $replace = [], ?string $locale = null): mixed
    {
        return $this->translator->get($key, $replace, $locale);
    }

    public function choice(string $key, array|Countable|float|int $number, array $replace = [], ?string $locale = null): string
    {
        return $this->translator->choice($key, $number, $replace, $locale);
    }

    public function getString(string $key, array $replace = [], ?string $locale = null): string
    {
        $translation = $this->get($key, $replace, $locale);

        if (! \is_string($translation)) {
            throw new UnexpectedValueException("Translation [$key] must be a string.");
        }

        return $translation;
    }

    public function getLocale(): string
    {
        return $this->translator->getLocale();
    }
}
