<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Laravel\Commands\Traits\HasListFormatting;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'moonshine:resources')]
class ResourcesCommand extends Command
{
    use HasListFormatting;

    protected $signature = 'moonshine:resources {--json}';

    protected $description = 'List all registered MoonShine resources';

    public function handle(CoreContract $core): int
    {
        $resources = $core->getResources();

        if ($resources->isEmpty()) {
            $this->components->warn('No resources registered.');

            return self::SUCCESS;
        }

        $rows = $this->getRows($resources);

        if ($this->option('json')) {
            $this->output->writeln($this->asJson($rows));
        } else {
            $this->output->writeln($this->forCli($rows));
        }

        return self::SUCCESS;
    }

    /**
     * @return Collection<int, array{type: string, class: string, url: string}>
     */
    protected function getRows(iterable $resources): Collection
    {
        $rows = new Collection();

        /** @var ResourceContract $resource */
        foreach ($resources as $resource) {
            $rows->push([
                'type' => 'resource',
                'class' => $resource::class,
                'url' => $resource->getUrl(),
            ]);

            /** @var PageContract $page */
            foreach ($resource->getPages() as $page) {
                $rows->push([
                    'type' => 'page',
                    'class' => $page::class,
                    'url' => $page->getUrl(),
                ]);
            }
        }

        return $rows;
    }

    protected function asJson(Collection $rows): string
    {
        $result = [];
        $currentResource = null;

        foreach ($rows as $row) {
            if ($row['type'] === 'resource') {
                if ($currentResource !== null) {
                    $result[] = $currentResource;
                }
                $currentResource = [
                    'class' => $row['class'],
                    'url' => $row['url'],
                    'pages' => [],
                ];
            } else {
                $currentResource['pages'][] = [
                    'class' => $row['class'],
                    'url' => $row['url'],
                ];
            }
        }

        if ($currentResource !== null) {
            $result[] = $currentResource;
        }

        return json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @return list<string>
     */
    protected function forCli(Collection $rows): array
    {
        $terminalWidth = $this->getTerminalWidth();

        $resourceCount = $rows->where('type', 'resource')->count();
        $pageCount = $rows->where('type', 'page')->count();

        return $rows->map(fn (array $row): string => $row['type'] === 'resource'
                ? $this->formatResourceLine($row, $terminalWidth)
                : $this->formatPageLine($row, $terminalWidth))
            ->prepend('')
            ->push('')
            ->push($this->formatCountOutput(
                \sprintf('Showing [%d] resources with [%d] pages', $resourceCount, $pageCount),
                $terminalWidth
            ))
            ->push('')
            ->toArray();
    }

    protected function formatResourceLine(array $row, int $terminalWidth): string
    {
        return $this->formatLine(
            left: $row['class'],
            right: $row['url'],
            prefix: '  <fg=blue;options=bold>RESOURCE</> ',
            prefixLength: 12,
            terminalWidth: $terminalWidth,
        );
    }

    protected function formatPageLine(array $row, int $terminalWidth): string
    {
        return $this->formatLine(
            left: $row['class'],
            right: $row['url'],
            prefix: '           <fg=#6C7280>↳</> ',
            prefixLength: 15,
            terminalWidth: $terminalWidth,
            leftBold: false,
            rightColor: '#6C7280',
        );
    }
}
