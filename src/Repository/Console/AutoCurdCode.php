<?php
/**
 * Created by PhpStorm.
 * User: Se1per
 * Date: 2023/8/13
 * Time: 1:40
 */

namespace App\Lib\Repository\Console;

use App\Lib\Repository\Console\src\AutoCodeHelp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class AutoCurdCode extends Command
{
    use AutoCodeHelp;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:code {apidoc?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'auto make CRUD Code';

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
        $tables = array_map('current', DB::select('SHOW TABLES'));

        $apiDoc = $this->argument('apidoc');

        foreach ($tables as $key => $val) {
            $write = true;

            $tableName = str_replace(env('DB_PREFIX'), '', $val);

            $tableName = $this->camelCase($tableName);

            if ($this->keyWordsBlackList($tableName)) continue;

            if(!$this->fileExistsIn(config('repository.controller').'\\'.$tableName.'Controller')){
                $this->makeControllerFunc($tableName);
            }else{
                $write = false;
            }

            if(!$this->fileExistsIn(config('repository.repository').'\\'.$tableName.'Repository')){

                $this->makeRepositoryFunc($tableName);
            }

            if(!$this->fileExistsIn(config('repository.services').'\\'.$tableName.'Services')){
                $this->makeServicesFunc($tableName);
            }

            if(!$this->fileExistsIn(config('repository.request').'\\'.$tableName.'Request')){
                $this->makeRequestFunc($tableName);
            }

            if(!$this->fileExistsIn(config('repository.model').'\\'.$tableName)){
                $this->makeModelFunc($tableName);
            }

            if($write){
                if($key == 0) {
                    $this->writeRouteUserFile($tables);
                }

                $this->writeRoute($tableName,count($tables),$key);
            }
        }
    }


    /**
     * makeModel
     * @param $tableName
     * @return void
     */
    public function makeModelFunc($tableName)
    {
        # 生成模型 Model
        if (!file_exists(config('repository.model') . '\\' . $tableName)) {
            Artisan::call('make:autoModel ' . $tableName);
            $output = Artisan::output();
            $this->info($output . '完成生成' . $tableName . '模型');
        }
    }

    /**
     * makeRepository
     * @param $tableName
     * @return void
     */
    public function makeRepositoryFunc($tableName)
    {
        # 生成 Repository
        if (!file_exists(config('repository.repository') . '\\' . $tableName . 'Repository')) {
            Artisan::call('make:autoRepository ' . $tableName . 'Repository');
            $output = Artisan::output();
            $this->info($output . '完成生成' . $tableName . '数据层');
        }
    }

    /**
     * makeServices
     * @param $tableName
     * @return void
     */
    public function makeServicesFunc($tableName)
    {
        # 生成 Service
        if (!file_exists(config('repository.service') . '\\' . $tableName . 'Services')) {
            Artisan::call('make:autoServices ' . $tableName . 'Services');
            $output = Artisan::output();
            $this->info($output . '完成生成' . $tableName . '逻辑处理层');
        }
    }

    /**
     * makeController
     * @param $tableName
     * @return void
     */
    public function makeControllerFunc($tableName)
    {
        # 生成 Controller
        if (!file_exists(config('repository.controller') . '\\' . $tableName . 'Controller')) {
            Artisan::call('make:autoController ' . $tableName . 'Controller');
            $output = Artisan::output();
            $this->info($output . '完成生成' . $tableName . '控制器层');
        }
    }
    /**
     * makeRequest
     * @param $tableName
     * @return void
     */
    public function makeRequestFunc($tableName)
    {
        # 生成 Request
        if (!file_exists(config('repository.request') . '\\' . $tableName . 'Request')) {
            Artisan::call('make:autoRequest ' . $tableName . 'Request');
            $output = Artisan::output();
            $this->info($output . '完成生成' . $tableName . '验证器层');
        }
    }

    public function makeControllerApiDocFunc($tableName)
    {
        # 生成 Controller
        if (!file_exists(config('repository.controller') . '\\' . $tableName . 'Controller')) {
            Artisan::call('make:autoControllerApiDoc ' . $tableName . 'Controller');
            $output = Artisan::output();
            $this->info($output . '完成生成' . $tableName . '控制器层');
        }
    }

}
