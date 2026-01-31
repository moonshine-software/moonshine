<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Commands\Traits;

use Symfony\Component\Console\Terminal;

/**
 * Provides route:list style formatting for console commands.
 *
 * Usage in commands:
 * - formatLine() - format a line with two columns and dots between
 * - formatCountOutput() - format a right-aligned count message
 * - truncate() - truncate string with ellipsis
 * - getTerminalWidth() - get terminal width
 */
trait HasListFormatting
{
    protected function getTerminalWidth(): int
    {
        return (new Terminal())->getWidth();
    }

    /**
     * Format a line with two columns separated by dots (route:list style).
     *
     * @param  string  $left  Left column text (e.g., class name, route name)
     * @param  string  $right  Right column text (e.g., URL, action)
     * @param  string  $prefix  Colored prefix with tags (e.g., '  <fg=blue>GET</> ')
     * @param  int  $prefixLength  Visible length of prefix (without color tags)
     * @param  int  $terminalWidth  Terminal width
     * @param  bool  $leftBold  Whether left column should be bold
     * @param  string  $leftColor  Color for left column
     * @param  string  $rightColor  Color for right column
     * @param  float  $leftRatio  Ratio of left column width (0.0-1.0)
     */
    protected function formatLine(
        string $left,
        string $right,
        string $prefix,
        int $prefixLength,
        int $terminalWidth,
        bool $leftBold = true,
        string $leftColor = 'white',
        string $rightColor = 'cyan',
        float $leftRatio = 0.5,
    ): string {
        $availableWidth = $terminalWidth - $prefixLength - 2;
        $leftWidth = (int) ($availableWidth * $leftRatio);
        $rightWidth = $availableWidth - $leftWidth;

        $displayLeft = $this->truncate($left, $leftWidth - 2);
        $dots = str_repeat('.', max($leftWidth - mb_strlen((string) $displayLeft) - 1, 1));
        $displayRight = $this->truncate($right, $rightWidth - 1);

        $leftStyle = $leftBold ? "fg=$leftColor;options=bold" : "fg=$leftColor";

        return \sprintf(
            '%s<%s>%s</> <fg=#6C7280>%s</> <fg=%s>%s</>',
            $prefix,
            $leftStyle,
            $displayLeft,
            $dots,
            $rightColor,
            $displayRight
        );
    }

    /**
     * Truncate a string with ellipsis if it exceeds max length.
     */
    protected function truncate(string $string, int $maxLength): string
    {
        if ($maxLength < 1) {
            return '';
        }

        if (mb_strlen($string) <= $maxLength) {
            return $string;
        }

        return mb_substr($string, 0, $maxLength - 1) . '…';
    }

    /**
     * Format a right-aligned count/summary message.
     */
    protected function formatCountOutput(string $text, int $terminalWidth): string
    {
        $offset = max($terminalWidth - mb_strlen($text) - 2, 0);
        $spaces = str_repeat(' ', $offset);

        return $spaces . '<fg=blue;options=bold>' . $text . '</>';
    }

    /**
     * Wrap output lines with empty lines and count message.
     *
     * @param  list<string>  $lines
     * @return list<string>
     */
    protected function wrapOutput(array $lines, string $countText, int $terminalWidth): array
    {
        return [
            '',
            ...$lines,
            '',
            $this->formatCountOutput($countText, $terminalWidth),
            '',
        ];
    }
}
