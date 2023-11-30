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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MakeRequest extends GeneratorCommand
{
    use AutoCodeHelp;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:autoRequest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'MakeRequest';

    protected function getStub()
    {
        $stub = __DIR__ . '/Stubs/request.stub';

        return $stub;
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return config('repository.request');
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
        $stub = $this->replaceName($stub); //替换自定义内容

        return parent::replaceClass($stub, $name);
    }

    protected function replaceCustomizeSetting($stub)
    {
        $tableName = $this->argument('name');
        $tableName = str_replace('Request', '', $tableName);

//        $tableName = $this->unCamelCase($tableName);
//        $dbPrefix = env('DB_PREFIX');
//        $sql = "SHOW TABLE STATUS LIKE '{$dbPrefix}{$tableName}';";
//        $tableComment = DB::select($sql);
//        $tableComment = array_shift($tableComment);

        $tableName = $this->unCamelCase($tableName);
        $dbPrefix = env('DB_PREFIX');
        $sql = 'SHOW FULL COLUMNS FROM `'.$dbPrefix.$tableName.'`;';
        $result = DB::select($sql);
//        $stub = str_replace('{{table}}', $tableName, $stub);
//        $result = Schema::getColumnListing($tableName);

        $store = '[';
        $put = '[';
        $get = '[';
        $msg = '[';
        $delete = '[';
        foreach ($result as $column) {
            # Store
            $this->makeStoreArray($tableName, $column->Field, $store,$msg,$column->Comment);
            $this->makeUpdateArray($tableName, $column->Field, $put,$column->Comment);
            $this->makeDeleteArray($tableName, $column->Field, $delete,$msg,$column->Comment);
            $this->makeGetArray($tableName, $column->Field, $get,$column->Comment);
        }
        $this->makeGetArrayPaginate($get,$msg);
        $store .= ']';
        $put .= ']';
        $get .= ']';
        $msg .= ']';
        $delete .= ']';

        $stub =str_replace('{{storeRules}}', $store, $stub);
        $stub =str_replace('{{updateRules}}', $put, $stub);
        $stub = str_replace('{{deleteRules}}', $delete, $stub);
        $stub = str_replace('{{getRules}}', $get, $stub);
        $stub =str_replace('{{messagesRules}}', $msg, $stub);

        return $stub;
    }

    public function replaceName($stub)
    {
        $tableName = $this->argument('name');
        $tableName = str_replace('Request', '', $tableName);

        $stub = str_replace('{{ table }}',$tableName, $stub);

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
