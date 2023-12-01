<?php
/**
 * Created by PhpStorm.
 * User: Se1per
 * Date: 2023/8/4 11:03
 */

namespace Japool\Genconsole\Base;

use Japool\Genconsole\Base\src\JsonCallBack;
use Illuminate\Support\Carbon;

abstract class BaseServices
{
    use JsonCallBack;

    /**
     * 通用构造查询条件
     * @param $makeData
     * @return array
     * User: Se1per
     * Date: 2023/8/4 11:08
     */
    public function selectArray($makeData): array
    {
        $sql = [];

        foreach ($makeData as $k =>$val)
        {
            if(isset($val)){
                if ($k == 'page' || $k == 'limit' && count($sql) <= 2) $sql['skip'][$k] = $val;
                if ($k == 'id'){
                    if(is_array($val)){
                        $sql['whereIn'] = $this->convertToWhereQuery('id','in',$val);
                    }else{
                        $sql['where'][] = $this->convertToWhereQuery('id','=',$val);
                    }
                }
//                if ($k == 'search') {
//                    $sql['whereLike'][] = $this->convertToWhereQuery('label_name','like',$val);
//                }
            }
        }
        return $sql;
    }

    /**
     * 构造查询条件
     * @param $column
     * @param $operator
     * @param $val
     * @param $operatorName
     * @return array|Carbon|mixed|string
     */
    public function convertToWhereQuery($column, $operator, $val, $operatorName = null)
    {
        if(is_string($val)){
            switch ($val) {
                case 'today':
                    $val = Carbon::today();
                    break;
                case 'yesterday':
                    $val = Carbon::yesterday();
                    break;
                case 'tomorrow':
                    $val = Carbon::tomorrow();
                    break;
            }
        }

        switch ($operator) {
            case 'like':
                return [$column, $operator, '%' . $val . '%', 'or'];
            case 'date':
            case 'day':
            case 'month':
            case 'year':
            case '=':
            case '<>':
            case '>':
            case '<':
            case '<=':
            case '>=':
                if($operatorName) return [$column, $operator, $val,$operatorName];
                return [$column, $operator, $val];
            case 'exists':
            case 'func':
            case 'raw':
                return $val;
            case 'notNull':
                return $column;
            default://in notIn
                return [$column, $val];
        }
    }
}
