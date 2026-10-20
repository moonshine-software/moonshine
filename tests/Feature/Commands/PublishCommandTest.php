<?php

declare(strict_types=1);

namespace MoonShine\Tests\Feature\Commands;

use Illuminate\Support\Facades\File;
use MoonShine\Laravel\Commands\PublishCommand;
use MoonShine\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

#[CoversClass(PublishCommand::class)]
#[Group('commands')]
final class PublishCommandTest extends TestCase
{
    #[Test]
    #[TestDox('it successful all created')]
    public function successfulAll(): void
    {
        $this->artisan(PublishCommand::class)
            ->expectsQuestion('Types', ['assets', 'resources', 'forms', 'pages'])
            ->expectsQuestion('Forms', ['login', 'filters'])
            ->expectsQuestion('Pages', ['profile', 'login', 'error'])
            // The checkout directory name differs between environments, so only the package path is checked.
            ->expectsOutputToContain('src/UI/dist')
            ->assertSuccessful();

        $basePath = __DIR__ . "/../../../app/MoonShine";

        $this->assertFileExists("$basePath/Forms/LoginForm.php");
        $this->assertFileExists("$basePath/Forms/FiltersForm.php");
        $this->assertFileExists("$basePath/Pages/ProfilePage.php");
        $this->assertFileExists("$basePath/Pages/LoginPage.php");
        $this->assertFileExists("$basePath/Pages/ErrorPage.php");

        $config = File::get(config_path('moonshine.php'));

        $this->assertStringContainsString("'login' => App\MoonShine\Forms\LoginForm::class", $config);
        $this->assertStringContainsString("'filters' => App\MoonShine\Forms\FiltersForm::class", $config);
        $this->assertStringContainsString("'login' => App\MoonShine\Pages\LoginPage::class", $config);
        $this->assertStringContainsString("'profile' => App\MoonShine\Pages\ProfilePage::class", $config);
        $this->assertStringContainsString("'error' => App\MoonShine\Pages\ErrorPage::class", $config);

    }
}
