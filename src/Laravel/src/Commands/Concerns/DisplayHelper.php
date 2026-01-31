<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Commands\Concerns;

use Laravel\Prompts\Concerns\Colors;
use MoonShine\Laravel\Commands\Enums\ConsoleTheme;

trait DisplayHelper
{
    use Colors;

    protected ?ConsoleTheme $theme = null;

    protected function initTheme(?ConsoleTheme $theme = null): void
    {
        $this->theme = $theme ?? ConsoleTheme::random();
    }

    protected function displayMoonShineHeader(string $featureName, ?ConsoleTheme $theme = null): void
    {
        $this->initTheme($theme);

        $this->displayGradientLogo();
        // $this->displayMoon();
        $this->displayTagline($featureName);
    }

    protected function displayGradientLogo(): void
    {
        $lines = [
            '███╗   ███╗ ██████╗  ██████╗ ███╗   ██╗███████╗██╗  ██╗██╗███╗   ██╗███████╗',
            '████╗ ████║██╔═══██╗██╔═══██╗████╗  ██║██╔════╝██║  ██║██║████╗  ██║██╔════╝',
            '██╔████╔██║██║   ██║██║   ██║██╔██╗ ██║███████╗███████║██║██╔██╗ ██║█████╗  ',
            '██║╚██╔╝██║██║   ██║██║   ██║██║╚██╗██║╚════██║██╔══██║██║██║╚██╗██║██╔══╝  ',
            '██║ ╚═╝ ██║╚██████╔╝╚██████╔╝██║ ╚████║███████║██║  ██║██║██║ ╚████║███████╗',
            '╚═╝     ╚═╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚══════╝╚═╝  ╚═╝╚═╝╚═╝  ╚═══╝╚══════╝',
        ];

        $gradient = $this->theme->gradient();

        $this->newLine();

        foreach ($lines as $index => $line) {
            $this->output->writeln($this->ansi256Fg($gradient[$index], $line));
        }
    }

    protected function displayMoon(): void
    {
        $moon = [
            '                                    ▄▄▄▄▄▄▄▄                                ',
            '                                 ▄██▀▀    ▀███▄                             ',
            '                                ██     ▄███████                             ',
            '                               ██▌    ██████████▌                           ',
            '                               ██▌    ██████████▌                           ',
            '                                ██     ▀███████                             ',
            '                                 ▀██▄▄    ▄███▀                             ',
            '                                    ▀▀▀▀▀▀▀▀                                ',
        ];

        $gradient = $this->theme->gradient();

        $this->newLine();

        foreach ($moon as $index => $line) {
            $colorIndex = (int) floor($index * 6 / 8);
            $this->output->writeln($this->ansi256Fg($gradient[$colorIndex], $line));
        }

        $this->newLine();
    }

    protected function displayTagline(string $featureName): void
    {
        $tagline = " MoonShine::{$featureName} ";
        $this->output->writeln($this->displayBadge($tagline));
        $this->newLine();
    }

    protected function displayOutro(string $text, string $link = ''): void
    {
        $this->newLine();
        $this->output->writeln(
            "\e[48;5;{$this->theme->primary()}m\e[30m\e[1m {$text}{$link} \e[0m"
        );
        $this->newLine();
    }

    protected function ansi256Fg(int $color, string $text): string
    {
        return "\e[38;5;{$color}m{$text}\e[0m";
    }

    protected function displayBadge(string $text): string
    {
        return "\e[48;5;{$this->theme->primary()}m\e[30m\e[1m{$text}\e[0m";
    }

    protected function hyperlink(string $label, string $url): string
    {
        return "\033]8;;{$url}\007{$label}\033]8;;\033\\";
    }
}
