<?php

namespace Za\Support\ApkUpdater;

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
    protected $signature = 'apk:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold the APK Version Update controller';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Http/Controllers/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->info('APK Version Update scaffolding generated successfully.');
    }
}
