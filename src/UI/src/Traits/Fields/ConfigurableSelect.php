<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use MoonShine\Support\DTOs\Select\FieldNames;

trait ConfigurableSelect
{
    protected bool $native = false;

    /**
     * @var array<string, array<string, mixed>>
     */
    protected array $plugins = [];

    /**
     * @var array<string, mixed>
     */
    protected array $settings = [];

    public function native(): static
    {
        $this->native = true;

        return $this;
    }

    protected function isNative(): bool
    {
        return $this->native;
    }

    /**
     * @param array{
     *     queryKey?: string,
     *     selectedValuesKey?: string,
     *     resultKey?: string,
     *     withAllFields?: bool
     * } $settings
     */
    public function asyncSettings(array $settings): static {
        $settings = array_replace([
            'queryKey' => null, // default: query
            'selectedValuesKey' => null,
            'resultKey' => null,
            'withAllFields' => false,
        ], $settings);

        return $this->customAttributes(array_filter([
            'data-async-query-key' => $settings['queryKey'],
            'data-async-selected-values-key' => $settings['selectedValuesKey'],
            'data-async-result-key' => $settings['resultKey'],
            'data-async-with-all-fields' => $settings['withAllFields'],
        ]));
    }

    /**
     * @param array<string, mixed> $settings
     */
    public function settings(array $settings): static {
        $this->settings = array_merge($this->settings, $settings);
        return $this;
    }

    /**
     * @param string|string[]|array<string, array<string, mixed>> $plugin
     * @param array<string, mixed> $pluginOptions
     */
    public function addPlugins(array|string $plugin, array $pluginOptions = []): static {
        if (is_array($plugin)) {
            foreach ($plugin as $name => $options) {
                if (is_numeric($name)) {
                    $name = $options;
                    $options = [];
                }

                $this->addPlugins($name, $options);
            }

            return $this;
        }

        $this->plugins[$plugin] = $pluginOptions;

        return $this;
    }

    public function fieldNames(FieldNames $names): static {
        return $this->settings(array_filter($names->toArray()));
    }

    public function createMode(
        ?string $filterRegex = null,
        bool $persist = true,
        bool $createOnBlur = false,
        bool $duplicates = false,
    ): static {
        if ($filterRegex) {
            $filterRegex = preg_match('/^(.)(.*)\1([a-zA-Z]*)$/s', $filterRegex, $matches)
                ? [
                    'pattern' => $matches[2],
                    'modifiers' => $matches[3]
                ]
                : null;
        }

        $settings = [
            'create' => true,
            'createFilter' => $filterRegex,
            'persist' => $persist,
            'createOnBlur' => $createOnBlur,
        ];

        if ($duplicates) {
            $settings['duplicates'] = true;
            $settings['hideSelected'] = false;
        }

        return $this->settings($settings);
    }

    public function maxItems(
        ?int $limit = null,
        ?string $text = null
    ): static {

        return $this->settings(array_filter([
            'maxItems' => $limit,
        ]))->addPlugins('max_items', [
            'text' => $text
        ]);
    }
}
