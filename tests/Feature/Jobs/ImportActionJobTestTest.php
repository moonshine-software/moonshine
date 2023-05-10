<?php

use MoonShine\Jobs\ImportActionJob;
use MoonShine\Models\MoonshineUser;

use function Pest\Laravel\assertDatabaseHas;

uses()->group('jobs');
uses()->group('mass-actions');

beforeEach(function () {
    $this->resource = $this->moonShineUserResource();
});

it('successful imported', function () {
    $path = 'moonshine_users/import.csv';

    $user = MoonshineUser::factory()->create(['name' => 'Testing']);

    Storage::fake('public');
    Storage::disk('public')->makeDirectory('moonshine_users');
    Storage::disk('public')->put($path, "ID,Name\n{$user->id},Updated name");

    $fullPath = Storage::disk('public')->path($path);

    $job = new ImportActionJob(
        $this->resource::class,
        $fullPath,
        true
    );

    $job->handle();

    Storage::disk('public')->assertMissing($path);

    assertDatabaseHas('moonshine_users', [
        'name' => 'Updated name'
    ]);
});
