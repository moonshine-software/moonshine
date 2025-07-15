<?php

declare(strict_types=1);

namespace MoonShine\Core\Support;

use Attribute;
use Closure;
use Illuminate\Support\Str;
use Leeto\FastAttributes\Attributes;
use MoonShine\Contracts\Core\DependencyInjection\CacheAttributesContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Support\Attributes\SearchUsingFullText;
use ReflectionAttribute;
use Stringable;

final readonly class CacheAttributes implements CacheAttributesContract
{
    public function __construct(private CoreContract $core)
    {
    }

    /**
     * @template T of mixed
     * @param  Closure(): T  $default
     * @param  class-string  $target
     * @param class-string<Attribute> $attribute
     * @param  int|null  $type
     * @param  string|null  $concrete
     * @param  string[]|null  $column
     *
     * @return T
     */
    public function get(
        Closure $default,
        string $target,
        string $attribute,
        ?int $type = null,
        ?string $concrete = null,
        ?array $column = null,
    ): mixed {
        $optimizer = $this->core->getOptimizer();

        if ($this->core->getOptimizer()->hasCache() && $attributes = $optimizer->getType(Attribute::class)) {
            $type ??= Attribute::TARGET_CLASS;
            $str = Str::of("$target.$type.$attribute")
                ->when(
                    $concrete !== null,
                    fn (Stringable $str) => $str->append(".$concrete"),
                );

            $find = static fn (?string $suffix = null, bool $withDefault = true) => data_get(
                $attributes,
                (string) $str->when(
                    $suffix !== null,
                    fn (Stringable $str) => $str->append(".$suffix"),
                ),
                $withDefault ? $default : null,
            );

            $key = array_key_first($column ?? []);

            if ($column === null) {
                return $find();
            }

            $value = reset($column);

            return $find((string) $value, false) ?? $find((string) $key);
        }

        return $default();
    }

    /**
     * @return array<class-string, array<int, array<string, mixed>>>
     */
    public function resolve(): array
    {
        $data = [];

        foreach ($this->core->getResources() as $resource) {
            /** @var ResourceContract $resource */
            $classAttributes = Attributes::for($resource)->get();

            foreach ($classAttributes as $attribute) {
                /** @var ReflectionAttribute<ResourceContract> $attribute */
                $data[$resource::class][Attribute::TARGET_CLASS][$attribute->getName()] = $attribute->getArguments();
            }

            /** @var null|SearchUsingFullText $search */
            $search = Attributes::for($resource, SearchUsingFullText::class)->method('search')->first();

            if ($search !== null) {
                $data[$resource::class][Attribute::TARGET_METHOD][$search::class] = [
                    'columns' => $search->columns,
                    'options' => $search->options,
                ];
            }
        }

        foreach ($this->core->getPages() as $page) {
            /** @var PageContract $page */
            /** @var ReflectionAttribute<PageContract>[] $classAttributes */
            $classAttributes = Attributes::for($page)->get();

            foreach ($classAttributes as $attribute) {
                /** @var ReflectionAttribute<PageContract> $attribute */
                $data[$page::class][Attribute::TARGET_CLASS][$attribute->getName()] = $attribute->getArguments();
            }
        }

        return $data;
    }
}
