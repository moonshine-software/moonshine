<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/config',
        __DIR__ . '/lang',
        __DIR__ . '/database',
        __DIR__ . '/routes',
        __DIR__ . '/src',
    ]);

    $rectorConfig->skip([
        __DIR__ . '/app',
        __DIR__ . '/public',
        __DIR__ . '/resources',
        __DIR__ . '/vendor',
        __DIR__ . '/stubs',
        __DIR__ . '/tests',
        __DIR__ . '/src/Http/Middleware/Authenticate.php'
    ]);

    $rectorConfig->importNames();
    $rectorConfig->importShortClasses();
    $rectorConfig->removeUnusedImports();

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_80,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::TYPE_DECLARATION,
    ]);
};
