<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use JsonException;

class Markdown extends Textarea
{
    protected string $view = 'moonshine::fields.markdown';

    protected static array $defaultOptions = [
        'previewClass' => ['prose', 'dark:prose-invert'],
        'forceSync' => true,
        'spellChecker' => false,
        'status' => false,
        'toolbar' => [ 'bold', 'italic', 'strikethrough', 'code', 'quote', 'horizontal-rule', '|', 'heading-1',
            'heading-2', 'heading-3', '|', 'table', 'unordered-list', 'ordered-list', '|', 'link', 'image', '|',
            'preview', 'side-by-side', 'fullscreen', '|', 'guide',
        ],
    ];

    protected array $options = [];

    protected static array $reservedOptions = [
        'element',
        'renderingConfig',
    ];

    public function getAssets(): array
    {
        return [
            'vendor/moonshine/libs/easymde/easymde.min.css',
            'vendor/moonshine/libs/easymde/easymde.min.js',
            'vendor/moonshine/libs/easymde/purify.min.js',
        ];
    }

    /**
     * @throws JsonException
     */
    public static function setDefaultOption(string $name, string|int|float|bool|array $value): void
    {
        if (in_array($name, static::$reservedOptions, true)) {
            return;
        }

        if (is_string($value) && str($value)->isJson()) {
            $value = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        }

        static::$defaultOptions[$name] = $value;
    }

    /**
     * @throws JsonException
     */
    public function addOption(string $name, string|int|float|bool|array $value): self
    {
        if (in_array($name, self::$reservedOptions, true)) {
            return $this;
        }

        if (is_string($value) && str($value)->isJson()) {
            $value = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        }

        $this->options[$name] = $value;

        return $this;
    }

    public function getOptions(): array
    {
        return array_merge(static::$defaultOptions, $this->options);
    }

    /**
     * @throws JsonException
     */
    public function toolbar(string|bool|array $value): self
    {
        $this->addOption('toolbar', $value);

        return $this;
    }

    protected function resolvePreview(): string
    {
        return (string) str()->markdown(
            $this->toFormattedValue() ?? ''
        );
    }

    /**
     * @return array<string, mixed>
     * @throws JsonException
     */
    protected function viewData(): array
    {
        return [
            'options' => json_encode($this->getOptions(), JSON_THROW_ON_ERROR),
        ];
    }
}
