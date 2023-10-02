<?php

declare(strict_types=1);

namespace MoonShine\Enums;

enum PageType: string
{
    case  INDEX = 'index-page';

    case  FORM = 'form-page';

    case  DETAIL = 'detail-page';

    public static function getTypeFromUri(string $uri): ?PageType
    {
        return match ($uri) {
            'index-page' => PageType::INDEX,
            'form-page' => PageType::FORM,
            'detail-page' => PageType::DETAIL,
            default => null
        };
    }
}
