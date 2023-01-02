<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property int $avatar
 * @property int $name
 * @property int $email
 */
final class MoonShineUserJsonResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'avatar' => $this->avatar,
            'email' => $this->email,
            'name' => $this->name,
        ];
    }
}
