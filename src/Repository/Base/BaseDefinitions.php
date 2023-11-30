<?php
/**
 * Created by PhpStorm.
 * User: Se1per
 * Date: 2023/10/12 14:49
 */

namespace App\Lib\Repository\Base;

use hg\apidoc\annotation as Apidoc;

class BaseDefinitions
{
    /**
     * 获取分页数据列表的参数
     * @Apidoc\Query("page",type="int",require=true,default="1",desc="查询页数")
     * @Apidoc\Query("limit",type="int",require=true,default="20",desc="查询条数")
     * @Apidoc\Returned("total", type="int", desc="总条数")
     */
    public function pagingParam(){}

}
