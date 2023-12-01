<?php
/**
 * Created by PhpStorm.
 * User: Se1per
 * Date: 2023/8/13
 * Time: 1:40
 */

namespace Japool\Genconsole\Console;

use Japool\Genconsole\Console\src\DelCodeHelp;
use Illuminate\Console\Command;

class AutoDelAutoCode extends Command
{
    use DelCodeHelp;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:delCode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'auto Del CRUD Code';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->getControllerFileList();

        $this->info('Delete code task completed');
    }


}
