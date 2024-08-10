<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

declare(strict_types=1);

namespace CWSPS154\FilamentAppSettings\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class CreateSettingsTab extends Command
{
    protected $signature = 'make:app-settings-tab {name}';
    protected $description = 'Generate a app settings tab';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * @throws FileNotFoundException
     */
    public function handle(): void
    {
        $name = $this->argument('name');

        $namespace = 'App\\Filament\\Settings\\Forms';
        $path = app_path('Filament/Settings/Forms/' . $name . '.php');

        if ($this->files->exists($path)) {
            $this->error('Class already exists!');
            return;
        }

        $this->makeDirectory($path);
        $content = $this->generateClassContent($namespace, $name);
        $this->files->put($path, $content);
        $this->info('Class created successfully.');
    }

    protected function makeDirectory($path): void
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0755, true);
        }
    }

    /**
     * @throws FileNotFoundException
     */
    protected function generateClassContent($namespace, $name): array|string
    {
        $stub = $this->files->get(__DIR__ . '/../stubs/tabClass.stubs');
        $state = Str::snake($name);
        return str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ state }}'],
            [$namespace, $name, $state],
            $stub
        );
    }
}
