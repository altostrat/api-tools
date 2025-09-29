<?php

namespace Altostrat\Tools\Console;

use Altostrat\Tools\Search\Searchable;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SearchReindexCommand extends Command
{
    protected $signature = 'search:reindex {--model=*} {--chunk=500}';

    protected $description = 'Reindex all searchable models for the search service.';

    public function handle(): int
    {
        if (empty(config('altostrat.search_dsn'))) {
            $this->error('Search is not configured. Please set the SEARCH_DSN environment variable.');

            return self::FAILURE;
        }

        $modelsToIndex = $this->getModelsToIndex();
        if ($modelsToIndex->isEmpty()) {
            $this->warn('No searchable models found to reindex.');

            return self::SUCCESS;
        }

        $chunkSize = (int) $this->option('chunk');
        $this->info('Reindexing the following models: '.$modelsToIndex->implode(', '));

        foreach ($modelsToIndex as $modelClass) {
            $this->reindexModel($modelClass, $chunkSize);
        }

        $this->info('✨ All searchable models have been reindexed.');

        return self::SUCCESS;
    }

    protected function reindexModel(string $modelClass, int $chunkSize): void
    {
        $this->line("Reindexing model [{$modelClass}]...");
        $query = $modelClass::query();
        $total = $query->count();

        if ($total === 0) {
            $this->warn("No records found for {$modelClass}. Skipping.");

            return;
        }

        $progressBar = $this->output->createProgressBar($total);
        $progressBar->start();

        $query->chunkById($chunkSize, function (Collection $models) use ($progressBar) {
            $models->each(fn (Model $model) => $model->publishSearchEvent('updated'));
            $progressBar->advance($models->count());
        });

        $progressBar->finish();
        $this->newLine(2);
    }

    protected function getModelsToIndex(): Collection
    {
        if ($this->option('model')) {
            return collect($this->option('model'))
                ->map(fn ($m) => $this->qualifyModel($m));
        }

        return $this->findAllSearchableModels();
    }

    protected function findAllSearchableModels(): Collection
    {
        $appPath = app_path();
        $files = File::allFiles($appPath);

        return collect($files)
            ->map(function ($file) use ($appPath) {
                $class = 'App\\'.str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    Str::after($file->getPathname(), $appPath.DIRECTORY_SEPARATOR)
                );

                return $class;
            })
            ->filter(fn ($class) => class_exists($class) && in_array(Searchable::class, class_uses_recursive($class)))
            ->values();
    }

    protected function qualifyModel(string $model): string
    {
        if (str_starts_with($model, 'App\\')) {
            return $model;
        }

        return 'App\\Models\\'.$model;
    }
}
