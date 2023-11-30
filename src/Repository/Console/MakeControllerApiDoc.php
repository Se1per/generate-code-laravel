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

class MakeControllerApiDoc extends GeneratorCommand
{
    use AutoCodeHelp;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:autoControllerApiDoc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'MakeControllerApiDoc';

    protected function getStub()
    {
        $stub = __DIR__ . '/Stubs/controllerApiDoc.stub';

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
        $smallTableName = lcfirst($this->unCamelCase($tableName));

        $tableComment = $this->getTableComment($tableName);
        $columns = $this->getTableColumnsComment($tableName);

        $save = '';
        $update = '';
        $del = '';
        $get = '';
        $get .= '\''.'page'.'\',' ;
        $get .= '\''.'limit'.'\',' ;
        $saveQuery = '';
        $updateQuery = '';
        $delQuery = '';
        $getQuery = '';
        foreach ($columns as $column) {

            $comment = empty($column->Comment) ? $column->Field :  $column->Comment;

            if($column->Field == 'deleted_at') continue;
            //Type
            $tableType = $this->convertDbTypeToPhpType($column->Type);

            $getQuery .= '     * @Apidoc\Returned('.'"'.$column->Field.'",'.'type="'.$tableType.'",'.'desc="'.$comment.'"'.'),'."\n";

            if($column->Field == 'id'){
                $update.=  '\''.$column->Field.'\',' ;
                $del.=  '\''.$column->Field.'\'' ;
                $get .=  '\''.$column->Field.'\',' ;

                $updateQuery .= '     * @Apidoc\Query('.'"'.$column->Field.'",'.'type="'.$tableType.'",'.'required=true'.',default="'.$column->Default.'",'.'desc="'.$comment.'"'.'),'."\n";

                $delQuery .= '     * @Apidoc\Query('.'"'.$column->Field.'",'.'type="'.$tableType.'",'.'required=true'.',default="'.$column->Default.'",'.'desc="'.$comment.'"'.'),'."\n";
                continue;
            }

            if($column->Null == 'NO'){
                $saveQuery .= '     * @Apidoc\Query('.'"'.$column->Field.'",'.'type="'.$tableType.'",'.'required=false'.',default="'.$column->Default.'",'.'desc="'.$column->Comment.'"'.'),'."\n";
                $updateQuery .= '     * @Apidoc\Query('.'"'.$column->Field.'",'.'type="'.$tableType.'",'.'required=false'.',default="'.$column->Default.'",'.'desc="'.$column->Comment.'"'.'),'."\n";
            }else{
                $saveQuery .= '     * @Apidoc\Query('.'"'.$column->Field.'",'.'type="'.$tableType.'",'.'required=true'.',default="'.$column->Default.'",'.'desc="'.$comment.'"'.'),'."\n";
                $updateQuery .= '     * @Apidoc\Query('.'"'.$column->Field.'",'.'type="'.$tableType.'",'.'required=true'.',default="'.$column->Default.'",'.'desc="'.$comment.'"'.'),'."\n";
            }

            if($column->Field == 'created_at' || $column->Field == 'updated_at'){
                $get .=  '\''.$column->Field.'\',' ;
                continue;
            }
            $get .=  '\''.$column->Field.'\',' ;
            $update.=  '\''.$column->Field.'\',' ;
            $save.=  '\''.$column->Field.'\',' ;
        }

        $comment = empty($tableComment->Comment) ? $tableComment->Name : $tableComment->Comment;

        $stub = str_replace('{{ApiDocTitle}}',$comment, $stub);
        $stub = str_replace('{{savefields}}',rtrim($save,','), $stub);
        $stub = str_replace('{{updatefields}}',rtrim($update,','), $stub);
        $stub = str_replace('{{delfields}}',rtrim($del,','), $stub);
        $stub = str_replace('{{getfields}}',rtrim($get,','), $stub);

        $stub = str_replace('{{apiQuery}}',$saveQuery, $stub);
        $stub = str_replace('{{apiQueryUpdate}}',$updateQuery, $stub);
        $stub = str_replace('{{apiQueryDel}}',$delQuery, $stub);
        $stub = str_replace('{{apiQueryGet}}',$getQuery, $stub);

        $apiUrl = config('repository.route')['api_url'];
        $stub = str_replace('{{url}}',$apiUrl, $stub);
        $stub = str_replace('{{smallTableName}}',$smallTableName, $stub);

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
