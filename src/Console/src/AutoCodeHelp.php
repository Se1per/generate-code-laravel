<?php
/**
 * Created by PhpStorm.
 * User: Se1per
 * Date: 2023/9/22 16:58
 */

namespace Japool\Genconsole\Console\src;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

trait AutoCodeHelp
{
    public function fileExistsIn($file)
    {
        if (is_file($file)) {
            return true;
        }
        return false;
    }

    /**
     * 驼峰命名转换
     * @param string $str
     * @return string
     * User: Se1per
     * Date: 2023/9/22 17:00
     */
    public function camelCase(string $str): string
    {
        $str = ucwords(str_replace(['-', '_'], ' ', $str));
        // 去除空格，并将第一个字母改为小写
        $str = str_replace(' ', '', $str);
        return ucfirst($str);
    }

    /**
     * 驼峰转下划线命名规则法
     * @param $str
     * @return string
     * User: Se1per
     * Date: 2023/9/22 17:08
     */
    public function unCamelCase($str): string
    {
        $str = preg_replace('/([A-Z])/', '_$1', $str);
        return strtolower(trim($str, '_'));
    }

    /**
     * 生成(新增)验证规则
     * @param $tableName
     * @param $column
     * @param $store
     * @param $msg
     * @return true|void
     * User: Se1per
     * Date: 2023/9/28 18:01
     */
    public function makeStoreArray($tableName, $column, &$store, &$msg, $comment = null)
    {
        if ($column == 'id' || $column == 'deleted_at' || $column == 'created_at' || $column == 'updated_at') {
            return true;
        }

        $type = Schema::getColumnType($tableName, $column);

        $tableComment = $comment == null ? $column : $comment;

        if ($type == 'string') {
            if (strstr($column, "name") || strstr($column, "title")) {
                $store .= '\'' . $column . '\'' . '=>' . '\'' . 'string|between:4,32' . '\'' . ',';
                $msg .= '\'' . $column . '.string' . '\'' . '=>' . '\'' . $tableComment . '必须是字符串' . '\'' . ',';
                $msg .= '\'' . $column . '.between' . '\'' . '=>' . '\'' . $tableComment . '长度必须4-32个字符串' . '\'' . ',';
            } else if (strstr($column, "id_card")) {
                $store .= '\'' . $column . '\'' . '=>' . '\'' . 'string|id_card|unique:' . $column . '\'' . ',';
                $msg .= '\'' . $column . '.string' . '\'' . '=>' . '\'' . $tableComment . '必须是字符串' . '\'' . ',';
                $msg .= '\'' . $column . '.id_card' . '\'' . '=>' . '\'' . $tableComment . '身份证格式不正确' . '\'' . ',';
                $msg .= '\'' . $column . '.unique' . '\'' . '=>' . '\'' . $tableComment . '已存在身份证号,请及时检查' . '\'' . ',';
            } else {
                $store .= '\'' . $column . '\'' . '=>' . '\'' . 'string' . '\'' . ',';
                $msg .= '\'' . $column . '.string' . '\'' . '=>' . '\'' . $tableComment . '必须是字符串' . '\'' . ',';
            }

            return true;
        }

        if (strstr($column, "phone") || strstr($column, "mobile")) {
            $store .= '\'' . $column . '\'' . '=>' . '\'' . 'integer|max:22|unique:' . $column . '\'' . ',';
            $msg .= '\'' . $column . '.integer' . '\'' . '=>' . '\'' . $tableComment . '必须是数字' . '\'' . ',';
            $msg .= '\'' . $column . '.max' . '\'' . '=>' . '\'' . $tableComment . '长度最大22个字符内' . '\'' . ',';
            $msg .= '\'' . $column . '.unique' . '\'' . '=>' . '\'' . $tableComment . '已存在手机号,请及时检查' . '\'' . ',';
            return true;
        }

        if (strstr($column, "type") || strstr($column, "status")) {
            $store .= '\'' . $column . '\'' . '=>' . '\'' . 'integer|digits_between:1,4' . '\'' . ',';
            $msg .= '\'' . $column . '.integer' . '\'' . '=>' . '\'' . $tableComment . '必须是数字' . '\'' . ',';
            $msg .= '\'' . $column . '.between' . '\'' . '=>' . '\'' . $tableComment . '长度必须1-4位之间' . '\'' . ',';
            return true;
        }

        if ($type == 'integer') {
            $store .= '\'' . $column . '\'' . '=>' . '\'' . 'integer|digits_between:1,11' . '\'' . ',';
            $msg .= '\'' . $column . '.integer' . '\'' . '=>' . '\'' . $tableComment . '必须是数字' . '\'' . ',';
            $msg .= '\'' . $column . '.digits_between' . '\'' . '=>' . '\'' . $tableComment . '长度必须1-11位之间' . '\'' . ',';
            return true;
        }

        if ($type == 'smallint') {
            $store .= '\'' . $column . '\'' . '=>' . '\'' . 'integer|digits_between:1,6' . '\'' . ',';
            $msg .= '\'' . $column . '.integer' . '\'' . '=>' . '\'' . $tableComment . '必须是数字' . '\'' . ',';
            $msg .= '\'' . $column . '.digits_between' . '\'' . '=>' . '\'' . $tableComment . '长度必须1-6位之间' . '\'' . ',';
            return true;
        }

        if ($type == 'datetime') {
            $store .= '\'' . $column . '\'' . '=>' . '\'' . 'date_format:Y-m-d H:i:s' . '\'' . ',';
            $msg .= '\'' . $column . '.date_format' . '\'' . '=>' . '\'' . $tableComment . '必须是日期格式' . '\'' . ',';
            return true;
        }

        if ($type == 'date') {
            $store .= '\'' . $column . '\'' . '=>' . '\'' . 'date_format:Y-m-d' . '\'' . ',';
            $msg .= '\'' . $column . '.date_format' . '\'' . '=>' . '\'' . $tableComment . '必须是日期格式' . '\'' . ',';
            return true;
        }
    }

    /**
     * 生成(更新)验证规则
     * @param $tableName
     * @param $column
     * @param $store
     * @param $msg
     * @return true|void
     * User: Se1per
     * Date: 2023/9/28 18:01
     */
    public function makeUpdateArray($tableName, $column, &$store, $msg = null, $comment = null)
    {
        if ($column == 'deleted_at' || $column == 'created_at' || $column == 'updated_at') {
            return true;
        }

        $type = Schema::getColumnType($tableName, $column);

        $tableComment = $comment == null ? $column : $comment;

        if ($type == 'string') {
            if (strstr($column, "name") || strstr($column, "title")) {
                $store .= '\'' . $column . '\'' . '=>' . '\'' . 'string|between:4,32' . '\'' . ',';
                $msg .= '\'' . $column . '.string' . '\'' . '=>' . '\'' . $tableComment . '必须是字符串' . '\'' . ',';
                $msg .= '\'' . $column . '.between' . '\'' . '=>' . '\'' . $tableComment . '长度必须4-32个字符串' . '\'' . ',';
            } elseif (strstr($column, "id_card")) {
                $store .= '\'' . $column . '\'' . '=>' . '\'' . 'string|id_card|unique:' . $column . '\'' . ',';
                $msg .= '\'' . $column . '.string' . '\'' . '=>' . '\'' . $tableComment . '必须是字符串' . '\'' . ',';
                $msg .= '\'' . $column . '.id_card' . '\'' . '=>' . '\'' . $tableComment . '身份证格式不正确' . '\'' . ',';
                $msg .= '\'' . $column . '.unique' . '\'' . '=>' . '\'' . $tableComment . '已存在身份证号,请及时检查' . '\'' . ',';
            } else {
                $store .= '\'' . $column . '\'' . '=>' . '\'' . 'string' . '\'' . ',';
                $msg .= '\'' . $column . '.string' . '\'' . '=>' . '\'' . $tableComment . '必须是字符串' . '\'' . ',';
            }
            return true;
        }

        if (strstr($column, "phone") || strstr($column, "mobile")) {
            $store .= '\'' . $column . '\'' . '=>' . '\'' . 'integer|max:22|unique:' . $column . '\'' . ',';
            $msg .= '\'' . $column . '.integer' . '\'' . '=>' . '\'' . $tableComment . '必须是数字' . '\'' . ',';
            $msg .= '\'' . $column . '.max' . '\'' . '=>' . '\'' . $tableComment . '长度最大22个字符内' . '\'' . ',';
            $msg .= '\'' . $column . '.unique' . '\'' . '=>' . '\'' . $tableComment . '已存在手机号,请及时检查' . '\'' . ',';
            return true;
        }

        if (strstr($column, "type") || strstr($column, "status")) {
            $store .= '\'' . $column . '\'' . '=>' . '\'' . 'integer|digits_between:1,4' . '\'' . ',';
            $msg .= '\'' . $column . '.integer' . '\'' . '=>' . '\'' . $tableComment . '必须是数字' . '\'' . ',';
            $msg .= '\'' . $column . '.between' . '\'' . '=>' . '\'' . $tableComment . '长度必须1-4位之间' . '\'' . ',';
            return true;
        }

        if ($type == 'integer') {
            if ($column == 'id') {
                $store .= '\'' . $column . '\'' . '=>' . '\'' . 'required|integer|digits_between:1,11|exists:' . $tableName . '\'' . ',';
                $msg .= '\'' . $column . '.required' . '\'' . '=>' . '\'' . $tableComment . '不能为空' . '\'' . ',';
                $msg .= '\'' . $column . '.integer' . '\'' . '=>' . '\'' . $tableComment . '必须是数字' . '\'' . ',';
                $msg .= '\'' . $column . '.digits_between' . '\'' . '=>' . '\'' . $tableComment . '长度必须1-11位之间' . '\'' . ',';
                $msg .= '\'' . $column . '.exists' . '\'' . '=>' . '\'' . $tableComment . '该数据不存在,请刷新后重试' . '\'' . ',';
            } else {
                $store .= '\'' . $column . '\'' . '=>' . '\'' . 'integer|digits_between:1,11' . '\'' . ',';
                $msg .= '\'' . $column . '.integer' . '\'' . '=>' . '\'' . $tableComment . '必须是数字' . '\'' . ',';
                $msg .= '\'' . $column . '.digits_between' . '\'' . '=>' . '\'' . $tableComment . '长度必须1-11位之间' . '\'' . ',';
            }
            return true;

        }

        if ($type == 'smallint') {
            if ($column == 'id') {
                $store .= '\'' . $column . '\'' . '=>' . '\'' . 'required|integer|digits_between:1,6|exists:' . $tableName . '\'' . ',';
                $msg .= '\'' . $column . '.required' . '\'' . '=>' . '\'' . $tableComment . '不能为空' . '\'' . ',';
                $msg .= '\'' . $column . '.integer' . '\'' . '=>' . '\'' . $tableComment . '必须是数字' . '\'' . ',';
                $msg .= '\'' . $column . '.digits_between' . '\'' . '=>' . '\'' . $tableComment . '长度必须1-6位之间' . '\'' . ',';
                $msg .= '\'' . $column . '.exists' . '\'' . '=>' . '\'' . $tableComment . '该数据不存在,请刷新后重试' . '\'' . ',';
            } else {
                $store .= '\'' . $column . '\'' . '=>' . '\'' . 'integer|digits_between:1,6' . '\'' . ',';
                $msg .= '\'' . $column . '.integer' . '\'' . '=>' . '\'' . $tableComment . '必须是数字' . '\'' . ',';
                $msg .= '\'' . $column . '.digits_between' . '\'' . '=>' . '\'' . $tableComment . '长度必须1-6位之间' . '\'' . ',';
            }
            return true;
        }

        if ($type == 'datetime') {
            $store .= '\'' . $column . '\'' . '=>' . '\'' . 'date_format:Y-m-d H:i:s' . '\'' . ',';
            $msg .= '\'' . $column . '.date_format' . '\'' . '=>' . '\'' . $tableComment . '必须是日期格式' . '\'' . ',';
            return true;
        }
        if ($type == 'date') {
            $store .= '\'' . $column . '\'' . '=>' . '\'' . 'date_format:Y-m-d' . '\'' . ',';
            $msg .= '\'' . $column . '.date_format' . '\'' . '=>' . '\'' . $tableComment . '必须是日期格式' . '\'' . ',';
            return true;
        }
    }

    /**
     * 生成(查询)验证规则
     * @param $tableName
     * @param $column
     * @param $store
     * @param $msg
     * @return true|void
     * User: Se1per
     * Date: 2023/9/28 18:01
     */
    public function makeGetArray($tableName, $column, &$store, $msg = null, $comment = null)
    {
        $type = Schema::getColumnType($tableName, $column);

        $tableComment = $comment == null ? $column : $comment;

        if ($type == 'string') {
            if (strstr($column, "name") || strstr($column, "title")) {
                $store .= '\'' . $column . '\'' . '=>' . '\'' . 'string|between:4,32' . '\'' . ',';
                $msg .= '\'' . $column . '.string' . '\'' . '=>' . '\'' . $tableComment . '必须是字符串' . '\'' . ',';
                $msg .= '\'' . $column . '.between' . '\'' . '=>' . '\'' . $tableComment . '长度必须4-32个字符串' . '\'' . ',';
            } elseif (strstr($column, "id_card")) {
                $store .= '\'' . $column . '\'' . '=>' . '\'' . 'string|id_card' . '\'' . ',';
                $msg .= '\'' . $column . '.string' . '\'' . '=>' . '\'' . $tableComment . '必须是字符串' . '\'' . ',';
                $msg .= '\'' . $column . '.id_card' . '\'' . '=>' . '\'' . $tableComment . '身份证格式不正确' . '\'' . ',';
            } else {
                $store .= '\'' . $column . '\'' . '=>' . '\'' . 'string' . '\'' . ',';
                $msg .= '\'' . $column . '.string' . '\'' . '=>' . '\'' . $tableComment . '必须是字符串' . '\'' . ',';
            }
            return true;
        }

        if (strstr($column, "phone") || strstr($column, "mobile")) {
            $store .= '\'' . $column . '\'' . '=>' . '\'' . 'integer|max:22' . '\'' . ',';
            $msg .= '\'' . $column . '.integer' . '\'' . '=>' . '\'' . $tableComment . '必须是数字' . '\'' . ',';
            $msg .= '\'' . $column . '.max' . '\'' . '=>' . '\'' . $tableComment . '长度最大22个字符内' . '\'' . ',';
            return true;
        }

        if (strstr($column, "type") || strstr($column, "status")) {
            $store .= '\'' . $column . '\'' . '=>' . '\'' . 'integer|between:0,10' . '\'' . ',';
            $msg .= '\'' . $column . '.integer' . '\'' . '=>' . '\'' . $tableComment . '必须是数字' . '\'' . ',';
            $msg .= '\'' . $column . '.between' . '\'' . '=>' . '\'' . $tableComment . '长度必须0-10' . '\'' . ',';
            return true;
        }

        if ($type == 'integer') {
            if ($column == 'id') {
                $store .= '\'' . $column . '\'' . '=>' . '\'' . 'integer|digits_between:1,11' . '\'' . ',';
                $msg .= '\'' . $column . '.integer' . '\'' . '=>' . '\'' . $tableComment . '必须是数字' . '\'' . ',';
                $msg .= '\'' . $column . '.digits_between' . '\'' . '=>' . '\'' . $tableComment . '长度必须1-11位之间' . '\'' . ',';
                $msg .= '\'' . $column . '.exists' . '\'' . '=>' . '\'' . $tableComment . '该数据不存在,请刷新后重试' . '\'' . ',';
            } else {
                $store .= '\'' . $column . '\'' . '=>' . '\'' . 'integer|digits_between:1,11' . '\'' . ',';
                $msg .= '\'' . $column . '.integer' . '\'' . '=>' . '\'' . $tableComment . '必须是数字' . '\'' . ',';
                $msg .= '\'' . $column . '.digits_between' . '\'' . '=>' . '\'' . $tableComment . '长度必须1-11位之间' . '\'' . ',';
            }
            return true;

        }

        if ($type == 'smallint') {
            if ($column == 'id') {
                $store .= '\'' . $column . '\'' . '=>' . '\'' . 'required|integer|digits_between:1,6' . '\'' . ',';
                $msg .= '\'' . $column . '.integer' . '\'' . '=>' . '\'' . $tableComment . '必须是数字' . '\'' . ',';
                $msg .= '\'' . $column . '.digits_between' . '\'' . '=>' . '\'' . $tableComment . '长度必须1-6位之间' . '\'' . ',';
                $msg .= '\'' . $column . '.exists' . '\'' . '=>' . '\'' . $tableComment . '该数据不存在,请刷新后重试' . '\'' . ',';
            } else {
                $store .= '\'' . $column . '\'' . '=>' . '\'' . 'integer|digits_between:1,6' . '\'' . ',';
                $msg .= '\'' . $column . '.integer' . '\'' . '=>' . '\'' . $tableComment . '必须是数字' . '\'' . ',';
                $msg .= '\'' . $column . '.digits_between' . '\'' . '=>' . '\'' . $tableComment . '长度必须1-6位之间' . '\'' . ',';
            }
            return true;
        }

        if ($type == 'datetime') {
            $store .= '\'' . $column . '\'' . '=>' . '\'' . 'date_format:Y-m-d H:i:s' . '\'' . ',';
            $msg .= '\'' . $column . '.date_format' . '\'' . '=>' . '\'' . $tableComment . '必须是日期格式' . '\'' . ',';
            return true;
        }

        if ($type == 'date') {
            $store .= '\'' . $column . '\'' . '=>' . '\'' . 'date_format:Y-m-d' . '\'' . ',';
            $msg .= '\'' . $column . '.date_format' . '\'' . '=>' . '\'' . $tableComment . '必须是日期格式' . '\'' . ',';
            return true;
        }

    }

    /**
     * 处理生成分页
     * @param $store
     * @param $msg
     * @return true
     */
    public function makeGetArrayPaginate(&$store, &$msg)
    {
        $store .= '\'' . 'page' . '\'' . '=>' . '\'' . 'integer' . '\'' . ',';
        $msg .= '\'' . 'page' . '.integer' . '\'' . '=>' . '\'' . '分页参数必须是数字' . '\'' . ',';

        $store .= '\'' . 'limit' . '\'' . '=>' . '\'' . 'integer' . '\'' . ',';
        $msg .= '\'' . 'limit' . '.integer' . '\'' . '=>' . '\'' . '分页参数必须是数字' . '\'' . ',';

        return true;
    }

    /**
     * 生成(删除)验证规则
     * @param $tableName
     * @param $column
     * @param $store
     * @param $msg
     * @return true|void
     * User: Se1per
     * Date: 2023/9/28 18:00
     */
    public function makeDeleteArray($tableName, $column, &$store, &$msg, $comment = null)
    {
        if ($column != 'id') return true;

        $type = Schema::getColumnType($tableName, $column);
        $tableComment = $comment == null ? $column : $comment;
        if ($type == 'integer') {
            $store .= '\'' . $column . '\'' . '=>' . '\'' . 'required|integer|digits_between:1,11|exists:' . $tableName . '\'' . ',';
            $msg .= '\'' . $column . '.required' . '\'' . '=>' . '\'' . $tableComment . '不能为空' . '\'' . ',';
            $msg .= '\'' . $column . '.integer' . '\'' . '=>' . '\'' . $tableComment . '必须是数字' . '\'' . ',';
            $msg .= '\'' . $column . '.digits_between' . '\'' . '=>' . '\'' . $tableComment . '长度必须1-11位之间' . '\'' . ',';
            $msg .= '\'' . $column . '.exists' . '\'' . '=>' . '\'' . $tableComment . '该数据不存在,请刷新后重试' . '\'' . ',';
            return true;
        }

        if ($type == 'smallint') {
            $store .= '\'' . $column . '\'' . '=>' . '\'' . 'required|integer|digits_between:1,6|exists:' . $tableName . '\'' . ',';
            $msg .= '\'' . $column . '.required' . '\'' . '=>' . '\'' . $tableComment . '不能为空' . '\'' . ',';
            $msg .= '\'' . $column . '.integer' . '\'' . '=>' . '\'' . $tableComment . '必须是数字' . '\'' . ',';
            $msg .= '\'' . $column . '.digits_between' . '\'' . '=>' . '\'' . $tableComment . '长度必须1-6位之间' . '\'' . ',';
            $msg .= '\'' . $column . '.exists' . '\'' . '=>' . '\'' . $tableComment . '该数据不存在,请刷新后重试' . '\'' . ',';
            return true;
        }
    }

    /**
     * 书写路由use
     * @param $table
     * @return void
     * User: Se1per
     * Date: 2023/10/10 17:31
     */
    public function writeRouteUserFile($table): void
    {
        $routePath = config('repository.route'); // 路由文件夹路径
        $filesCheck = File::isFile($routePath['patch'] . '\\' . $routePath['route_api']);

        if (!$filesCheck) {
            echo '无法找到配置文件中得路由写入路径,请检查配置文件';
            exit;
        }

        foreach ($table as $val) {
            $tableName = str_replace(env('DB_PREFIX'), '', $val);
            $tableName = $this->camelCase($tableName);

            if ($this->keyWordsBlackList($tableName)) continue;

            $userName = config('repository.controller') . '\\' . $tableName . 'Controller';
            $str = 'use ' . $userName . ';';
            $str .= "\n";
            File::append($routePath['patch'] . '\\' . $routePath['route_api'], $str);
        }
    }

    /**
     * 书写路由内容
     * @param $tableName
     * @param $tableNum
     * @param $key
     * @return void
     * User: Se1per
     * Date: 2023/10/10 17:32
     */
    public function writeRoute($tableName, $tableNum, $key): void
    {
        $routePath = config('repository.route'); // 路由文件夹路径
        $filesCheck = File::isFile($routePath['patch'] . '\\' . $routePath['route_api']);

        if (!$filesCheck) {
            echo '无法找到配置文件中得路由写入路径,请检查配置文件';
            exit;
        }

        $files = File::get($routePath['patch'] . '\\' . $routePath['route_api']);

        $small = lcfirst($tableName);
        $dbPrefix = env('DB_PREFIX');
        $sql = "SHOW TABLE STATUS LIKE '{$dbPrefix}{$tableName}';";
        $tableComment = DB::select($sql);
        $tableComment = array_shift($tableComment);

        if ($key == 0) {
            // 判断是否是 API 路由文件
            if (str_contains($files, "Route::group(['prefix' => '" . env('APP_NAME') . "'], function () {")) {
                echo '请清空路由文件夹中得' . env('APP_NAME') . '组,让于重新生成';
                exit;
            }

            $str = "Route::group(['prefix' => '" . env('APP_NAME') . "'], function () {";

        } else {
            $str = '';
        }

        if ($tableComment) {
            $str .= '//' . $tableComment->Comment . '路由';
        } else {
            $str .= '//' . $tableName . '路由';
        }

        $str .= "\n\t";
        $str .= "Route::group(['prefix' => '" . $small . "', 'name' => '" . $small . "'], function () {";
        $str .= "\n\t";
        $str .= "    Route::post('save" . $tableName . "Data', [" . $tableName . "Controller::class, 'save" . $tableName . "Data'])->name('save" . $tableName . "Data');";
        $str .= "\n\t";
        $str .= "    Route::put('update" . $tableName . "Data', [" . $tableName . "Controller::class, 'update" . $tableName . "Data'])->name('update" . $tableName . "Data');";
        $str .= "\n\t";
        $str .= "    Route::put('del" . $tableName . "Data', [" . $tableName . "Controller::class, 'del" . $tableName . "Data'])->name('del" . $tableName . "Data');";
        $str .= "\n\t";
        $str .= "    Route::get('get" . $tableName . "Data', [" . $tableName . "Controller::class, 'get" . $tableName . "Data'])->name('get" . $tableName . "Data');";
        $str .= "\n\t";
        $str .= "});";

        if ($tableNum == $key + 1) {
            $str .= "\n\t";
            $str .= "});";
        }

        File::append($routePath['patch'] . '\\' . $routePath['route_api'], $str);

    }

    /**
     * 转换数据库数据类型
     * @param $dbType
     * @return string|null
     * User: Se1per
     * Date: 2023/10/13 10:19
     */
    public function convertDbTypeToPhpType($dbType): ?string
    {
        $type = strtolower($dbType);

        if (strpos($type, 'int') !== false) {
            return 'integer';
        }

        if (strpos($type, 'decimal') !== false || strpos($type, 'float') !== false || strpos($type, 'double') !== false) {
            return 'float';
        }

        if (strpos($type, 'boolean') !== false) {
            return 'boolean';
        }

        if (strpos($type, 'string') !== false || strpos($type, 'char') !== false || strpos($type, 'text') !== false) {
            return 'string';
        }

        if (strpos($type, 'blob') !== false || strpos($type, 'binary') !== false) {
            return 'binary';
        }

        if (strpos($type, 'json') !== false) {
            return 'json';
        }

        if (strpos($type, 'date') !== false || strpos($type, 'timestamp') !== false) {
            return 'date';
        }

        // 如果数据库字段类型不在上述情况中，返回null

        return null;
    }

    /**
     * 黑名单关键字过滤
     * @param $tableName
     * @return bool|mixed true不生成 false 不处理
     * User: Se1per
     * Date: 2023/10/17 11:05
     */
    public function keyWordsBlackList($tableName): mixed
    {
        return array_reduce(config('repository.intermediate_table'), function ($carry, $item) use ($tableName) {
            return $carry || stripos($tableName, $item) !== false;
        }, false);
    }

    /**
     * 根据表名获取表详情
     * @param $tableName
     * @return mixed
     * User: Se1per
     * Date: 2023/10/17 11:10
     */
    public function getTableComment($tableName)
    {
        $dbPrefix = env('DB_PREFIX');
        $sql = "SHOW TABLE STATUS LIKE '{$dbPrefix}{$tableName}';";
        $tableComment = DB::select($sql);
        return $tableComment = array_shift($tableComment);
    }

    /**
     * 获取表内字段详情
     * @param $tableName
     * @return array
     * User: Se1per
     * Date: 2023/10/17 11:17
     */
    public function getTableColumnsComment($tableName)
    {
        $dbPrefix = env('DB_PREFIX');
        $tableDetails = 'SHOW FULL COLUMNS FROM ' . $dbPrefix . $tableName;
        return DB::select($tableDetails);
    }
}
