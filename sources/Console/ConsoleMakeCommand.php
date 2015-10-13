<?php

namespace LaravelPlus\Extension\Console;

use Illuminate\Foundation\Console\ConsoleMakeCommand as BaseCommand;
use LaravelPlus\Extension\Addons\Addon;

class ConsoleMakeCommand extends BaseCommand
{
    use GeneratorCommandTrait;

    /**
     * The console command singature.
     *
     * @var stringphp
     */
    protected $signature = 'make:console
        {name : The name of the class}
        {--addon= : The name of the addon}
        {--command=command.name : The terminal command that should be assigned}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[+] Create a new Artisan command';

    /**
     * Get the destination class base path.
     *
     * @param \LaravelPlus\Extension\Addons\Addon $addon
     *
     * @return string
     */
    protected function getBasePath(Addon $addon)
    {
        return $addon->path('classes');
    }
}