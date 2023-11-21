<?php

namespace Za\Support\LanguageSwitch;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

class ControllerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'language:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold the LanguageSwitch controller';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (! is_dir($directory = app_path('Http/Controllers/LanguageSwitch'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Http/Controllers/LanguageSwitch/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->info('LanguageSwitch scaffolding generated successfully.');
    }
}
