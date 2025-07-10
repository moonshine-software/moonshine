<?php

declare(strict_types=1);

namespace MoonShine\UI\Applies;

use MoonShine\Contracts\Core\DependencyInjection\AppliesRegisterContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\HasCoreContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Contracts\UI\ApplyContract;
use MoonShine\Contracts\UI\FormElementContract;
use MoonShine\Core\Traits\WithCore;

final class AppliesRegister implements AppliesRegisterContract, HasCoreContract
{
    use WithCore;

    private string $type = 'fields';

    private ?string $for = null;

    private string $defaultFor = ResourceContract::class;

    /**
     * @var non-empty-array<string, array[]|array<class-string<FormElementContract>, class-string<ApplyContract>>>
     */
    private array $applies = [
        'filters' => [],
        'fields' => [],
    ];

    public function __construct(
        CoreContract $core
    ) {
        $this->setCore($core);
    }

    public function type(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param  class-string  $for
     */
    public function for(string $for): static
    {
        $this->for = $for;

        return $this;
    }

    /**
     * @return class-string
     */
    public function getFor(): string
    {
        return $this->for ?? $this->getDefaultFor();
    }

    /**
     * @param  class-string  $for
     */
    public function defaultFor(string $for): static
    {
        $this->defaultFor = $for;

        return $this;
    }

    /**
     * @return class-string
     */
    public function getDefaultFor(): string
    {
        return $this->defaultFor;
    }

    public function filters(): static
    {
        $this->type('filters');

        return $this;
    }

    public function fields(): static
    {
        $this->type('fields');

        return $this;
    }

    /**
     * @param  ?class-string  $for
     */
    public function findByField(
        FormElementContract $field,
        string $type = 'fields',
        ?string $for = null
    ): ?ApplyContract {
        if ($field->hasOnApply()) {
            return null;
        }

        return $this->getCore()->getContainer(AppliesRegisterContract::class)
            ->type($type)
            ->for($for ?? $this->getDefaultFor())
            ->get($field::class);
    }

    /**
     * @param  class-string<FormElementContract>  $fieldClass
     * @param  class-string<ApplyContract>  $applyClass
     */
    public function add(string $fieldClass, string $applyClass): static
    {
        $this->applies[$this->type][$this->getFor()][$fieldClass] = $applyClass;

        return $this;
    }

    /**
     * @param  array<class-string<FormElementContract>, class-string<ApplyContract>>  $data
     */
    public function push(array $data): static
    {
        $this->applies[$this->type][$this->getFor()] = array_merge(
            $this->applies[$this->type][$this->getFor()] ?? [],
            $data,
        );

        return $this;
    }

    /**
     * @param  class-string<FormElementContract>  $fieldClass
     */
    public function get(string $fieldClass, ?ApplyContract $default = null): ?ApplyContract
    {
        $apply = $this->applies[$this->type][$this->getFor()][$fieldClass] ?? null;

        if (\is_null($apply)) {
            foreach ($this->applies[$this->type][$this->getFor()] ?? [] as $fieldApply => $applyClass) {
                if (is_subclass_of($fieldClass, $fieldApply)) {
                    $apply = $applyClass;

                    break;
                }
            }
        }

        if (\is_null($apply) && ! \is_null($default)) {
            $apply = $default;
        }

        if (\is_null($apply)) {
            return null;
        }

        if ($apply instanceof ApplyContract) {
            return $apply;
        }

        /** @var class-string<ApplyContract> $apply */
        return $this->getCore()->getContainer($apply);
    }
}
