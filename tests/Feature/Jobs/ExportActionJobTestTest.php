<?php

use MoonShine\Jobs\ExportActionJob;
use MoonShine\Models\MoonshineUser;

uses()->group('jobs');
uses()->group('mass-actions');

beforeEach(function () {
    $this->resource = $this->moonShineUserResource();
});

it('successful exported', function () {
    $path = 'moonshine_users/export.csv';

    Storage::fake('public');
    Storage::disk('public')->makeDirectory('moonshine_users');

    $fullPath = Storage::disk('public')->path($path);

    MoonshineUser::factory()->create(['name' => 'Testing']);

    $job = new ExportActionJob(
        $this->resource::class,
        $fullPath,
        'public',
        'moonshine_users'
    );

    $job->handle();

    Storage::disk('public')->assertExists($path);

    expect(File::get($fullPath))
        ->toContain('Testing');
});
