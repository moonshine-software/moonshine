<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

final class TinyMce extends Field
{
    protected static string $view = 'moonshine::fields.tinymce';

    public string $plugins = 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed linkchecker a11ychecker tinymcespellchecker permanentpen powerpaste advtable advcode editimage tinycomments tableofcontents footnotes mergetags autocorrect typography inlinecss';

    public string $toolbar = 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat';

    public array $mergeTags = [];

    public string $commentAuthor = 'Author name';

    public function getAssets(): array
    {
        return [
            "https://cdn.tiny.cloud/1/{$this->token()}/tinymce/{$this->version()}/tinymce.min.js"
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

    protected function token(): string
    {
        return config('moonshine.tinymce.token', '');
    }

    protected function version(): string
    {
        return (string) config('moonshine.tinymce.version', 6);
    }
}
