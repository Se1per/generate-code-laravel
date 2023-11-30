<?php
/**
 * Created by PhpStorm.
 * User: Se1per
 * Date: 2023/2/6
 * Time: 16:24
 */

namespace App\Lib\Repository\Controller\src;

trait callClass
{

    public function getProjectClass($name, $arg = [])
    {
        try {
            /* 容器加载 */
            if (class_exists($name)) {
                return app($name);
            }

            /* 原生写法 */
//            $reflection = new ReflectionClass($name);
//            if ($reflection->isInstantiable()) {
//                return $reflection->newInstance($arg);
//            }

        } catch (\ReflectionException $e) {
            return $e->getMessage();
        }

        return null;
    }

}
