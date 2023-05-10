<?php

use MoonShine\Dashboard\Dashboard;
use MoonShine\Dashboard\DashboardBlock;
use MoonShine\Dashboard\TextBlock;

uses()->group('dashboard');

it('make instance', function () {
    $instance = new Dashboard();

    $instance->registerBlocks([
        DashboardBlock::make([
            TextBlock::make('Label', 'Text'),
        ]),
    ]);

    expect($instance)
        ->getBlocks()
        ->toBeCollection()
        ->toHaveCount(1)
        ->each->toBeInstanceOf(DashboardBlock::class);
});
