<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use MoonShine\Support\DTOs\Select\AsyncSettings;
use MoonShine\Support\DTOs\Select\FieldsNames;
use MoonShine\Support\DTOs\Select\Settings;

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
     * @param array<string, mixed>|AsyncSettings $settings
     */
    public function asyncSettings(array|AsyncSettings $settings): static
    {
        if (\is_array($settings)) {
            $settings = AsyncSettings::make($settings);
        }

        return $this->customAttributes($settings->toArray(), true);
    }

    public function asyncWithFields(): static
    {
        return $this->asyncSettings(
            AsyncSettings::make()->withAllFields()
        );
    }

    /**
     * @param array<string, mixed> $settings
     */
    public function settings(array|Settings $settings): static
    {
        if (\is_array($settings)) {
            $settings = Settings::make($settings);
        }

        $this->settings = array_merge($this->settings, $settings->toArray());

        return $this;
    }

    /**
     * @param string|string[] $plugin
     * @param array<string, mixed> $pluginOptions
     */
    public function addPlugin(array|string $plugin, array $pluginOptions = []): static
    {
        foreach ((array)$plugin as $name) {
            $this->plugins[$name] = $pluginOptions;
        }

        return $this;
    }

    public function fieldsNames(FieldsNames $names): static
    {
        return $this->settings(array_filter($names->toArray()));
    }

    public function selectCreatable(
        ?string $filterRegex = null,
        bool $persist = true,
        bool $createOnBlur = false,
        bool $duplicates = false,
        bool $addPrecedence = false,
    ): static {
        if ($filterRegex) {
            $filterRegex = preg_match('/^(.)(.*)\1([a-zA-Z]*)$/s', $filterRegex, $matches)
                ? [
                    'pattern' => $matches[2],
                    'modifiers' => $matches[3],
                ]
                : null;
        }

        $settings = [
            'create' => true,
            'createFilter' => $filterRegex,
            'persist' => $persist,
            'createOnBlur' => $createOnBlur,
            'addPrecedence' => $addPrecedence,
        ];

        if ($duplicates) {
            $settings['duplicates'] = true;
            $settings['hideSelected'] = false;
        }

        return $this->settings($settings);
    }

    public function selectMaxItems(
        ?int $limit = null,
        ?string $text = null
    ): static {
        return $this->settings(array_filter([
            'maxItems' => $limit,
        ]))->addPlugin('max_items', [
            'text' => $text,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getSelectViewData(): array
    {
        return [
            'isNative' => $this->isNative(),
            'isSearchable' => $this->isSearchable(),
            'settings' => $this->settings,
            'plugins' => $this->plugins,
        ];
    }
}
