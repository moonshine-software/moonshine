<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Closure;
use MoonShine\Support\DTOs\Select\Option;
use MoonShine\Support\DTOs\Select\OptionGroup;
use MoonShine\Support\DTOs\Select\OptionProperty;
use MoonShine\Support\DTOs\Select\Options;

trait ConfigureSelect
{
    protected bool $native = false;

    protected array $plugins = [];

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
     *     queryKey: ?string,
     *     selectedValuesKey: ?string,
     *     resultKey: ?string,
     *     withAllFields: bool
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

    public function settings(array $settings): static {
        $this->settings = array_merge($this->settings, $settings);
        return $this;
    }

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

    public function fieldNames(
        ?string $valueField = null,         // default: value
        ?string $labelField = null,         // default: label
        ?string $descriptionField = null,   // default: description

        ?string $childrenField = null,      // default: values
        ?string $optgroupValueField = null, // default: value
        ?string $optgroupLabelField = null, // default: label
        ?string $optgroupField = null,      // default: optgroup

        ?array  $searchField = null,         // default: ['label']
        ?string $disabledField = null,      // default: disabled
        ?string $sortField = null           // default: $order
    ): static {
        if (is_null($searchField) && ! is_null($labelField)) {
            $searchField = [$labelField];
        }

        return $this->settings(
            array_filter(compact(
                'valueField',
                'labelField',
                'descriptionField',
                'childrenField',
                'optgroupValueField',
                'optgroupLabelField',
                'optgroupField',
                'searchField',
                'disabledField',
                'sortField',
            ))
        );
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
        ?int $limit = null
    ): static {

        return $this->settings(array_filter([
            'maxItems' => $limit,
        ]))->addPlugins('max_items');
    }
}
