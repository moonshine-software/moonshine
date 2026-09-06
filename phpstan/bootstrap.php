<?php

declare(strict_types=1);

use MoonShine\Laravel\Providers\MoonShineServiceProvider;

// Testbench does not discover the root package's provider automatically.
// Register the real macros and command signatures for Larastan.
app()->register(MoonShineServiceProvider::class);
