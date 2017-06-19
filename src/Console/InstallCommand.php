<?php namespace Bugotech\Http\Console;

use Bugotech\IO\Filesystem;
use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'http:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install structure to http and routing';

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * Create a new service provider instance.
     *
     * @param \Bugotech\IO\Filesystem $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $dir_public = public_path();
        $this->files->force($dir_public);

        $this->files->copy(__DIR__ . '/../../public/index.php', $this->files->combine($dir_public, 'index.php'));
        $this->files->copy(__DIR__ . '/../../public/robots.txt', $this->files->combine($dir_public, 'robots.txt'));
        $this->files->copy(__DIR__ . '/../../public/.htaccess', $this->files->combine($dir_public, '.htaccess'));

        $this->info('HTTP installed');
    }
}
