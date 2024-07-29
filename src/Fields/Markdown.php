<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;

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

    public static function setDefaultOption(string $name, string|int|float|bool|array $value): void
    {
        if (in_array($name, static::$reservedOptions)) {
            return;
        }

        if (is_string($value) && str($value)->isJson()) {
            $value = json_decode($value, true);
        }

        static::$defaultOptions[$name] = $value;
    }

    public function getAssets(): array
    {
        return [
            'vendor/moonshine/libs/easymde/easymde.min.css',
            'vendor/moonshine/libs/easymde/easymde.min.js',
            'vendor/moonshine/libs/easymde/purify.min.js',
        ];
    }

    public function addOption(string $name, string|int|float|bool|array $value): self
    {
        if (in_array($name, static::$reservedOptions)) {
            return $this;
        }

        if (is_string($value) && str($value)->isJson()) {
            $value = json_decode($value, true);
        }

        $this->options[$name] = $value;

        return $this;
    }

    public function getOptions(): array
    {
        return array_merge(static::$defaultOptions, $this->options);
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'options' => json_encode($this->getOptions()),
        ];
    }
}
