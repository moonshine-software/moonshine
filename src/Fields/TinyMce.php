<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use JsonException;
use MoonShine\AssetManager\Js;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class TinyMce extends Textarea
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

    public static string $token = '';

    public static string $version = '6';

    public static ?string $fileManagerUrl = null;

    public function getAssets(): array
    {
        $assets = [
            Js::make('vendor/moonshine/libs/tinymce/tinymce.min.js'),
        ];

        if ($this->getToken()) {
            $assets[] = Js::make("https://cdn.tiny.cloud/1/{$this->getToken()}/tinymce/{$this->getVersion()}/plugins.min.js");
        }

        return $assets;
    }

    public static function token(string $token): void
    {
        self::$token = $token;
    }

    protected function getToken(): string
    {
        return self::$token;
    }

    public static function version(string $version): void
    {
        self::$token = $version;
    }

    protected function getVersion(): string
    {
        return self::$version;
    }

    public static function fileManager(string $url): void
    {
        self::$fileManagerUrl = $url;
    }

    protected function getFileManagerUrl(): ?string
    {
        return self::$fileManagerUrl;
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
            'file_picker_callback',
            'language',
            'plugins',
            'menubar',
            'toolbar',
            'tinycomments_mode',
            'tinycomments_author',
            'mergetags_list',
        ];

        if (! in_array($name, $reservedNames)) {
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

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws JsonException
     */
    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

        $this->customAttributes([
            'data-toolbar_mode' => 'sliding',
            'data-language' => !empty($this->locale) ? $this->locale : app()->getLocale(),
            'data-plugins' => trim($this->plugins . ' ' . $this->addedPlugins),
            'data-menubar' => trim($this->menubar),
            'data-toolbar' => trim($this->toolbar . ' ' . $this->addedToolbar),
            'data-tinycomments_mode' => !empty($this->commentAuthor) ? 'embedded' : null,
            'data-tinycomments_author' => !empty($this->commentAuthor) ? $this->commentAuthor : null,
            'data-mergetags_list' => !empty($this->mergeTags)
                ? json_encode($this->mergeTags, JSON_THROW_ON_ERROR)
                : null,
            'data-file_manager' => $this->getFileManagerUrl(),
        ]);
    }
}
