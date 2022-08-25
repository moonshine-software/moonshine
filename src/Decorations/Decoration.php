<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Decorations;

use JsonSerializable;
use Leeto\MoonShine\Contracts\Fields\HasFields;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithComponent;
use Leeto\MoonShine\Traits\WithFields;

abstract class Decoration implements HasFields, JsonSerializable
{
    use Makeable;
    use WithComponent;
    use WithFields;

    protected string $label;

    final public function __construct(string $label, array $fields = [])
    {
        $this->setLabel($label);
        $this->fields($fields);
    }

    /**
     * Get id of decoration
     *
     * @return string
     */
    public function id(): string
    {
        return (string) str($this->label())->slug();
    }

    /**
     * Get name of decoration
     *
     * @return string
     */
    public function name(): string
    {
        return $this->id();
    }

    /**
     * Get label of decoration
     *
     * @return string
     */
    public function label(): string
    {
        return $this->label;
    }

    /**
     * Define label for decoration
     *
     * @param  string  $label
     * @return $this
     */
    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'component' => $this->getComponent(),
            'label' => $this->label(),
            'id' => $this->id(),
            'fields' => $this->getFields(),
        ];
    }
}
