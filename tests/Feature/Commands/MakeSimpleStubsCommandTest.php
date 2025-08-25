<?php

declare(strict_types=1);

namespace MoonShine\Tests\Feature\Commands;

use MoonShine\Laravel\Commands\MakeApplyCommand;
use MoonShine\Laravel\Commands\MakeControllerCommand;
use MoonShine\Laravel\Commands\MakeHandlerCommand;
use MoonShine\Laravel\Commands\MakePolicyCommand;
use MoonShine\Laravel\Commands\MakeTypeCastCommand;
use MoonShine\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

#[CoversClass(MakeApplyCommand::class)]
#[CoversClass(MakePolicyCommand::class)]
#[CoversClass(MakeControllerCommand::class)]
#[CoversClass(MakeTypeCastCommand::class)]
#[CoversClass(MakeHandlerCommand::class)]
#[Group('commands')]
final class MakeSimpleStubsCommandTest extends TestCase
{
    public static function commands(): array
    {
        return [
            [MakeApplyCommand::class, 'MoonShine/Applies', null, true],
            [MakePolicyCommand::class, 'Policies', 'DeleteMePolicy', false],
            [MakeControllerCommand::class, 'MoonShine/Controllers', null, true],
            [MakeTypeCastCommand::class, 'MoonShine/TypeCasts', null, true],
            [MakeHandlerCommand::class, 'MoonShine/Handlers', null, true],
        ];
    }

    #[Test]
    #[TestDox('it successful file created')]
    #[DataProvider('commands')]
    public function successfulCreated(string $command, string $dir, ?string $nameOfFile = null, bool $withSub = false): void
    {
        $name = $nameOfFile ?? 'DeleteMe';
        $file = "$name.php";
        $path = __DIR__ . "/../../../app/$dir/$file";

        @unlink($path);

        $this->assertFileDoesNotExist($path);

        $this->artisan($command, [
            'className' => $name,
        ])->expectsOutputToContain(
            "$name was created",
        )->assertSuccessful();

        $this->assertFileExists($path);
    }

    #[Test]
    #[TestDox('it successful file created in sub folder')]
    #[DataProvider('commands')]
    public function successfulCreatedInSubFolder(string $command, string $dir, ?string $nameOfFile = null, bool $withSub = false): void
    {
        if (! $withSub) {
            $this->markTestSkipped('Not defined');
        }

        $subDir = 'Test';
        $name = $nameOfFile ?? 'DeleteMe';
        $file = "$name.php";
        $path = __DIR__ . "/../../../app/$dir/Test/$file";

        @unlink($path);

        $this->assertFileDoesNotExist($path);

        $this->artisan($command, [
            'className' => "/$subDir/$name",
        ])->expectsOutputToContain(
            "$name was created",
        )->assertSuccessful();

        $this->assertFileExists($path);
    }
}
