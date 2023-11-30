<?php
/**
 * Created by PhpStorm.
 * User: Se1per
 * Date: 2023/8/4 15:58
 */

namespace App\Lib\Repository\Base;

use App\Http\Controllers\Controller;
use App\Lib\Repository\Controller\src\callClass;
use App\Lib\Repository\Controller\src\Validates;

abstract class BaseController extends Controller
{
    use Validates,callClass;

    protected $Repository;
    protected $Services;
    protected $controllerName;
    protected $config;

    public function __construct()
    {
        $this->controllerName = str_replace('Controller', '', class_basename(get_class($this)));
        $this->config = config('repository');

        if (!empty($this->controllerName)) {
//            $repositoryPatch = 'App\\Http\\Repository\\' . $this->controllerName . 'Repository';
            $repositoryPatch = $this->config['repository'] .'\\'. $this->controllerName . 'Repository';
//            $serverPatch = 'App\\Http\\Server\\' . $this->controllerName . 'Server';
            $serverPatch = $this->config['services'] .'\\'. $this->controllerName . 'Services';

            if (class_exists($repositoryPatch)) {
//                $repositoryPatch = new ReflectionClass($repositoryPatch);
//
//                if ($repositoryPatch->isInstantiable()) {
//                    return $repositoryPatch->newInstance();
//                }
//                $this->Repository = $this->app->make($repositoryPatch);
                $this->Repository = app($repositoryPatch);
            }

            if (class_exists($serverPatch)) {
//                $serverPatch = new ReflectionClass($serverPatch);
//
//                if ($serverPatch->isInstantiable()) {
//                    return $serverPatch->newInstance();
//                }
//                $this->Server = $this->app->make($serverPatch);
                $this->Services = app($serverPatch);
            }
        }
        return null;
    }

    public function deleteData()
    {
        $data = Request()->only(['id']);

        try {

            list($status, $msg) = $this->Repository->deleteData($data['id']);

            if ($status) {
                return $this->Server->JsonMain(200000, '数据删除成功');
            }

        } catch (\Exception $e) {
            return $this->Server->JsonMain(200002, $e->getMessage());
        }

        //503004
        return $this->Server->JsonMain(503004, $msg);
    }


}
