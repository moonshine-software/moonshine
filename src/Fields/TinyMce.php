<?php

declare(strict_types=1);

namespace MoonShine\Fields;

final class TinyMce extends Textarea
{
    protected string $view = 'moonshine::fields.tinymce';

    public string $plugins = 'anchor autolink autoresize charmap codesample code emoticons image link lists advlist media searchreplace table visualblocks wordcount directionality fullscreen help nonbreaking pagebreak preview visualblocks visualchars';

    public string $addedPlugins = '';

    public string $menubar = 'file edit insert view format table tools';

    public string $toolbar = 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table tabledelete hr nonbreaking pagebreak | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat | codesample | ltr rtl | tableprops tablerowprops tablecellprops | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol | fullscreen preview print visualblocks visualchars code | help';

    public string $addedToolbar = '';

    public array $mergeTags = [];

    public string $commentAuthor = '';

    public string $locale = '';

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

    public function plugins(string $plugins): self
    {
        $this->plugins = $plugins;

        return $this;
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

    public function addConfig(string $name, bool|int|float|string $value): self
    {
        $name = str($name)->lower()->value();
        $reservedNames = [
            'selector',
            'path_absolute',
            'file_manager',
            'relative_urls',
            'branding',
            'skin',
            'content_css',
            'file_picker_callback',
            'language',
            'plugins',
            'menubar',
            'toolbar',
            'tinycomments_mode',
            'tinycomments_author',
            'mergetags_list',
        ];

        if (!in_array($name, $reservedNames)) {
            $this->customAttributes(["data-$name" => $value]);
        }

        return $this;
    }

    public function addPlugins(string $plugins): self
    {
        $this->addedPlugins = $plugins;

        return $this;
    }

    public function addToolbar(string $toolbar): self
    {
        $this->addedToolbar = $toolbar;

        return $this;
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
}
