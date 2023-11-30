<?php
/**
 * Created by PhpStorm.
 * User: Se1per
 * Date: 2023/8/13
 * Time: 11:44
 */

namespace App\Lib\Repository\Console;

//use App\Lib\Repository\Console\src\ConsoleHelpFunc;
use App\Lib\Repository\Console\src\AutoCodeHelp;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Schema;

class MakeModel extends GeneratorCommand
{
    use AutoCodeHelp;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:autoModel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'MakeModel';

    protected function getStub()
    {
        $stub = __DIR__ . '/Stubs/model.stub';

        return $stub;
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return config('repository.model');
    }

    /**
     * 设置类名和自定义替换内容
     * @param string $stub
     * @param string $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $stub = $this->replaceCustomizeSetting($stub); //替换自定义内容

        return parent::replaceClass($stub, $name);
    }

    protected function replaceCustomizeSetting($stub)
    {
        $tableName = $this->argument('name');
        $tableName = $this->unCamelCase($tableName);

        $stub = str_replace('{{table}}',$tableName, $stub);

        $columns = Schema::getColumnListing($tableName);
        $result = '';
        foreach ($columns as $column) {
            $result .=  '\''.$column.'\',' ;
        }
        $stub = str_replace('{{fields}}',$result, $stub);

        return $stub;
    }

    /**
     * Create a new command instance.
     *
     * @return void
     */
//    public function __construct()
//    {
//        parent::__construct();
//    }

    /**
     * Execute the console command.
     *
     * @return int
     */
//    public function handle()
//    {
//        $this->newVariable = 1234;
//
//        // 下面继续执行其他生成命令的逻辑
//        return 0;
//    }
}
