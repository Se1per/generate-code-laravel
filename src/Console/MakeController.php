<?php
/**
 * Created by PhpStorm.
 * User: Se1per
 * Date: 2023/8/13
 * Time: 11:44
 */

namespace Japool\Genconsole\Console;

//use App\Lib\Repository\Console\src\ConsoleHelpFunc;
use Japool\Genconsole\Console\src\AutoCodeHelp;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MakeController extends GeneratorCommand
{
    use AutoCodeHelp;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:autoController';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'MakeController';

    protected function getStub()
    {
        $stub = __DIR__ . '/Stubs/controller.stub';

        return $stub;
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return config('repository.controller');
    }

    /**
     * 设置类名和自定义替换内容
     * @param string $stub
     * @param string $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $stub = $this->replaceName($stub); //替换自定义内容
        $stub = $this->replaceCustomizeSetting($stub); //替换自定义内容

        return parent::replaceClass($stub, $name);
    }

    public function replaceName($stub)
    {
        $tableName = $this->argument('name');
        $tableName = str_replace('Controller', '', $tableName);
        $stub = str_replace('{{ table }}',$tableName, $stub);
        return $stub;
    }

    protected function replaceCustomizeSetting($stub)
    {
        $tableName = $this->argument('name');
        $tableName = str_replace('Controller', '', $tableName);
        $tableName = $this->unCamelCase($tableName);
//        $smallTableName = lcfirst($this->unCamelCase($tableName));
//        $dbPrefix = env('DB_PREFIX');
//        $sql = "SHOW TABLE STATUS LIKE '{$dbPrefix}{$tableName}';";
//        $tableComment = DB::select($sql);
//        $tableComment = array_shift($tableComment);
//        $tableDetails = 'SHOW FULL COLUMNS FROM '.$dbPrefix.$tableName;
//        $columns = DB::select($tableDetails);

        $columns = $this->getTableColumnsComment($tableName);

        $save = '';
        $update = '';
        $del = '';
        $get = '';
        $get .= '\''.'page'.'\',' ;
        $get .= '\''.'limit'.'\',' ;
        foreach ($columns as $column) {
            if($column->Field == 'deleted_at') continue;
            if($column->Field == 'id'){
                $update.=  '\''.$column->Field.'\',' ;
                $del.=  '\''.$column->Field.'\'' ;
                $get .=  '\''.$column->Field.'\',' ;
                continue;
            }

            if($column->Field == 'created_at' || $column->Field == 'updated_at'){
                $get .=  '\''.$column->Field.'\',' ;
                continue;
            }
            $get .=  '\''.$column->Field.'\',' ;
            $update.=  '\''.$column->Field.'\',' ;
            $save.=  '\''.$column->Field.'\',' ;
        }

        $stub = str_replace('{{savefields}}',rtrim($save,','), $stub);
        $stub = str_replace('{{updatefields}}',rtrim($update,','), $stub);
        $stub = str_replace('{{delfields}}',rtrim($del,','), $stub);
        $stub = str_replace('{{getfields}}',rtrim($get,','), $stub);

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
