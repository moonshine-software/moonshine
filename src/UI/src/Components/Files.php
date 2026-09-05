<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\DTOs\FileItem;

/** @method static static make(list<string|FileItem|array{full_path?: null|string, raw_value?: null|string, name?: null|string, attributes?: null|array<string, mixed>}> $files = [], bool $download = true) */
final class Files extends MoonShineComponent
{
    protected string $view = 'moonshine::components.files';

    /** @var list<array<string, mixed>> */
    public array $files;

    /**
     * @param list<string|FileItem|array{full_path?: null|string, raw_value?: null|string, name?: null|string, attributes?: null|array<string, mixed>}> $files
     */
    public function __construct(
        array $files = [],
        public bool $download = true,
    ) {
        parent::__construct();

        $this->files = array_map(
            static function (string|FileItem|array $value): array {
                if ($value instanceof FileItem) {
                    return $value->toArray();
                }

                if (\is_string($value)) {
                    return new FileItem($value, $value, $value)->toArray();
                }

                return new FileItem(
                    $value['full_path'] ?? '',
                    $value['raw_value'] ?? '',
                    $value['name'] ?? '',
                    new MoonShineComponentAttributeBag($value['attributes'] ?? []),
                )->toArray();
            },
            $files,
        );
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'files' => $this->files,
        ];
    }
}
