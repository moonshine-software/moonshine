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

#[AsCommand(name: 'moonshine:pages')]
class PagesCommand extends Command
{
    use HasListFormatting;

    protected $signature = 'moonshine:pages {--json}';

    protected $description = 'List all registered MoonShine pages (excluding resource pages)';

    public function handle(CoreContract $core): int
    {
        $pages = $core->getPages();

        if ($pages->isEmpty()) {
            $this->components->warn('No pages registered.');

            return self::SUCCESS;
        }

        $resourcePageClasses = $this->getResourcePageClasses($core);
        $rows = $this->getRows($pages, $resourcePageClasses);

        if ($rows->isEmpty()) {
            $this->components->warn('No standalone pages registered (resource pages are shown in moonshine:resources).');

            return self::SUCCESS;
        }

        if ($this->option('json')) {
            $this->output->writeln($this->asJson($rows));
        } else {
            $this->output->writeln($this->forCli($rows));
        }

        return self::SUCCESS;
    }

    /**
     * @return array<class-string, true>
     */
    protected function getResourcePageClasses(CoreContract $core): array
    {
        $resourcePageClasses = [];

        /** @var ResourceContract $resource */
        foreach ($core->getResources() as $resource) {
            /** @var PageContract $resourcePage */
            foreach ($resource->getPages() as $resourcePage) {
                $resourcePageClasses[$resourcePage::class] = true;
            }
        }

        return $resourcePageClasses;
    }

    /**
     * @param  array<class-string, true>  $resourcePageClasses
     * @return Collection<int, array{class: string, url: string}>
     */
    protected function getRows(iterable $pages, array $resourcePageClasses): Collection
    {
        $rows = new Collection();

        /** @var PageContract $page */
        foreach ($pages as $page) {
            if (isset($resourcePageClasses[$page::class])) {
                continue;
            }

            $rows->push([
                'class' => $page::class,
                'url' => $page->getUrl(),
            ]);
        }

        return $rows;
    }

    protected function asJson(Collection $rows): string
    {
        return json_encode($rows->values()->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @return list<string>
     */
    protected function forCli(Collection $rows): array
    {
        $terminalWidth = $this->getTerminalWidth();

        return $rows->map(fn (array $row): string => $this->formatLine(
            left: $row['class'],
            right: $row['url'],
            prefix: '  <fg=yellow;options=bold>PAGE</> ',
            prefixLength: 8,
            terminalWidth: $terminalWidth,
        ))
            ->prepend('')
            ->push('')
            ->push($this->formatCountOutput(
                \sprintf('Showing [%d] pages', $rows->count()),
                $terminalWidth
            ))
            ->push('')
            ->toArray();
    }
}
