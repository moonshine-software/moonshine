<?php

declare(strict_types=1);

namespace MoonShine\Tests\Feature\Commands;

use Illuminate\Support\Facades\File;
use MoonShine\ColorManager\Palettes\NeutralPalette;
use MoonShine\Laravel\Commands\MakeLayoutCommand;
use MoonShine\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

#[CoversClass(MakeLayoutCommand::class)]
#[Group('commands')]
final class LayoutCommandTest extends TestCase
{
    #[Test]
    #[TestDox('it successful file created')]
    public function successfulCreated(): void
    {
        $name = 'DeleteMe';
        $file = "$name.php";
        $path = __DIR__ . "/../../../app/MoonShine/Layouts/$file";

        @unlink($path);

        $this->assertFileDoesNotExist($path);

        $this->artisan(MakeLayoutCommand::class, [
            'className' => $name,
        ])
            ->expectsQuestion('Select a palette', NeutralPalette::class)
            ->expectsQuestion('Use the default template in the system?', 'yes')
            ->expectsOutputToContain(
                "$name was created"
            )
            ->assertSuccessful();

        $config = File::get(config_path('moonshine.php'));
        $this->assertStringContainsString("'layout' => App\MoonShine\Layouts\DeleteMe::class", $config);

        $this->assertFileExists($path);
    }

    #[Test]
    #[TestDox('it successful file created in sub folder')]
    public function successfulCreatedInSubFolder(): void
    {
        $dir = 'Test';
        $name = 'DeleteMe';
        $file = "$name.php";
        $path = __DIR__ . "/../../../app/MoonShine/Layouts/Test/$file";

        @unlink($path);

        $this->assertFileDoesNotExist($path);

        $this->artisan(MakeLayoutCommand::class, [
            'className' => "$dir/$name",
        ])
            ->expectsQuestion('Select a palette', NeutralPalette::class)
            ->expectsQuestion('Use the default template in the system?', 'yes')
            ->expectsOutputToContain(
                "$name was created"
            )
            ->assertSuccessful();

        $config = File::get(config_path('moonshine.php'));

        $this->assertStringContainsString("'layout' => App\MoonShine\Layouts\Test\DeleteMe::class", $config);

        $this->assertFileExists($path);
    }
}
