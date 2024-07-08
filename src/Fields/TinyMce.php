<?php

declare(strict_types=1);

namespace MoonShine\Fields;

class TinyMce extends Textarea
{
    protected string $view = 'moonshine::fields.tinymce';

    public array $plugins = [
        'anchor', 'autolink', 'autoresize', 'charmap', 'codesample', 'code', 'emoticons', 'image', 'link',
        'lists', 'advlist', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount', 'directionality',
        'fullscreen', 'help', 'nonbreaking', 'pagebreak', 'preview', 'visualblocks', 'visualchars',
    ];

    public string $menubar = 'file edit insert view format table tools';

    public string $toolbar = 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table tabledelete hr nonbreaking pagebreak | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat | codesample | ltr rtl | tableprops tablerowprops tablecellprops | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol | fullscreen preview print visualblocks visualchars code | help';

    public string $addedToolbar = '';

    public array $mergeTags = [];

    public string $commentAuthor = '';

    public string $locale = '';

    public array $config = [];

    public function getAssets(): array
    {
        $assets = ["vendor/moonshine/libs/tinymce/tinymce.min.js"];

        if ($this->token()) {
            $assets[] = "https://cdn.tiny.cloud/1/{$this->token()}/tinymce/{$this->version()}/plugins.min.js";
        }

        return $assets;
    }

    protected function token(): string
    {
        return config('moonshine.tinymce.token', '');
    }

    protected function version(): string
    {
        return (string) config('moonshine.tinymce.version', 6);
    }

    public function mergeTags(array $mergeTags): self
    {
        $this->mergeTags = $mergeTags;

        return $this;
    }

    public function commentAuthor(string $commentAuthor): self
    {
        $this->commentAuthor = $commentAuthor;

        return $this;
    }

    public function plugins(string|array $plugins): self
    {
        if (is_string($plugins)) {
            $plugins = explode(' ', $plugins);
        }

        $this->plugins = $plugins;

        return $this;
    }

    public function getPlugins(): array
    {
        $plugins = $this->plugins;

        return collect($plugins)->unique()->toArray();
    }

    public function menubar(string $menubar): self
    {
        $this->menubar = $menubar;

        return $this;
    }

    public function toolbar(string $toolbar): self
    {
        $this->toolbar = $toolbar;

        return $this;
    }

    public function addConfig(string $name, mixed $value): self
    {
        $name = str($name)->lower()->value();

        $reservedNames = [
            'selector',
            'path_absolute',
            'file_manager',
            'relative_urls',
            'branding',
            'skin',
            'file_picker_callback',
            'language',
            'plugins',
            'menubar',
            'toolbar',
            'tinycomments_mode',
            'tinycomments_author',
            'mergetags_list',
        ];

        if (is_string($value) && str($value)->isJson()) {
            $value = json_decode($value, true);
        }

        if (! in_array($name, $reservedNames)) {
            $this->config[$name] = $value;
        }

        return $this;
    }

    public function addPlugins(string|array $plugins): self
    {
        if (is_string($plugins)) {
            $plugins = explode(' ', $plugins);
        }

        $this->plugins = array_merge($this->plugins, $plugins);

        return $this;
    }

    public function removePlugins(string|array $plugins): self
    {
        if (is_string($plugins)) {
            $plugins = explode(' ', $plugins);
        }

        $this->plugins = array_diff($this->plugins, $plugins);

        return $this;
    }

    public function addToolbar(string $toolbar): self
    {
        $this->addedToolbar = $toolbar;

        return $this;
    }

    public function getConfig(): array
    {
        return [
            'toolbar_mode' => 'sliding',
            'language' => ! empty($this->locale) ? $this->locale : app()->getLocale(),
            'plugins' => implode(' ', $this->getPlugins()),
            'menubar' => trim($this->menubar),
            'toolbar' => trim($this->toolbar . ' ' . $this->addedToolbar),
            'tinycomments_mode' => ! empty($this->commentAuthor) ? 'embedded' : null,
            'tinycomments_author' => ! empty($this->commentAuthor) ? $this->commentAuthor : null,
            'mergetags_list' => ! empty($this->mergeTags) ? json_encode($this->mergeTags) : null,
            'file_manager' => config('moonshine.tinymce.file_manager', false) ? config('moonshine.tinymce.file_manager', 'laravel-filemanager') : null,
            ...$this->config,
        ];
    }

    public function locale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    protected function resolveValue(): string
    {
        return str($this->toValue())->replace(
            ['&amp;', '&lt;', '&gt;', '&nbsp;', '&quot;'],
            ['&amp;amp;', '&amp;lt;', '&amp;gt;', '&amp;nbsp;', '&amp;quot;']
        )->value();
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'config' => json_encode($this->getConfig()),
        ];
    }
}
