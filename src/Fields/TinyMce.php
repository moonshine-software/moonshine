<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;

final class TinyMce extends Textarea
{
    protected static string $view = 'moonshine::fields.tinymce';

    public string $plugins = 'anchor autolink autoresize charmap codesample code emoticons image link lists advlist media searchreplace table visualblocks wordcount directionality fullscreen help nonbreaking pagebreak preview visualblocks visualchars';

    public string $addedPlugins = '';

    public string $toolbar = 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table tabledelete hr nonbreaking pagebreak | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat | codesample | ltr rtl | tableprops tablerowprops tablecellprops | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol | fullscreen preview print visualblocks visualchars code | help';

    public string $addedToolbar = '';

    public array $mergeTags = [];

    public string $commentAuthor = '';

    public function getAssets(): array
    {
        return [
            "vendor/moonshine/libs/tinymce/tinymce.min.js",
            "https://cdn.tiny.cloud/1/{$this->token()}/tinymce/{$this->version()}/plugins.min.js",
        ];
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

    public function toolbar(string $toolbar): self
    {
        $this->toolbar = $toolbar;

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

    protected function token(): string
    {
        return config('moonshine.tinymce.token', '');
    }

    protected function version(): string
    {
        return (string) config('moonshine.tinymce.version', 6);
    }

    public function formViewValue(Model $item): string
    {
        return str(parent::formViewValue($item))->replace(
            ['&amp;', '&lt;', '&gt;', '&nbsp;', '&quot;'],
            ['&amp;amp;', '&amp;lt;', '&amp;gt;', '&amp;nbsp;', '&amp;quot;']
        )->value();
    }
}
