<?php
/**
 * Created by PhpStorm.
 * User: Se1per
 * Date: 2023/8/4 16:23
 */

namespace Japool\Genconsole\Base;

use Japool\Genconsole\Eloquent\Repository;

abstract class BaseRepository extends Repository
{
    public function model()
    {
        return config('repository')['model'] . '\\' . str_replace('Repository', '', class_basename(get_class($this)));
    }

    /**
     * 调用with
     * @param $withName
     * @param $withType
     * @return $this
     * User: Se1per
     * Date: 2023/8/4 16:34
     */
    public function witFunctionPack($withName, $withType = 'with'): BaseRepository
    {
        $with = [];

        if (is_array($withName)) {
            $with = $withName;
        } else {
            $with[] = $withName;
        }

        foreach ($with as $k => $val) {
            if (isset($this->withMapping[$k])) {
                if ($val) {
                    $this->$withType([$this->withMapping[$k] => $val]);
                } else {
                    $this->$withType([$this->withMapping[$k]]);
                }
            }
        }

        return $this;
    }

    /**
     * 执行数据层构造
     * @param array $data
     * @param bool $needToArray
     * @param string $get get|find
     * @return mixed
     * @throws \ErrorException
     */
    public function getData(array $data = [], string $get = 'get', bool $needToArray = false)
    {
        return $this->runningSql($data, $needToArray, $get);
    }

    /**
     * 执行数据层构造
     * @param array $data
     * @return mixed
     * @throws \ErrorException
     */
    public function getCount(array $data = [])
    {
        return $this->runningSql($data, false, 'count');
    }

    public function saveData($data)
    {
        try {
            $createData = $this->model->create($data);
        } catch (\Exception $e) {
            return [false, $e->getMessage()];
        }

        return [true, $createData];
    }

    public function saveDataAll($data)
    {
        try {

            $createData = $this->model->insert($data);

        } catch (\Exception $e) {

            return [false, $e->getMessage()];
        }

        return [true, $createData];
    }

    public function updateData($data, $id, $attribute = "id")
    {
        try {

            if (isset($data['id'])) unset ($data['id']);

            # 监听
//            $old = $this->model->where(...$where)->first();
//            $obs = new GlobalObserver;
//            $obs->updated($old,$data);

            $model = $this->model->where($attribute, '=', $id)->update($data);

//            $this->refresh();
//
//            $model = $this->model;

        } catch (\Exception $e) {
            return [false, $e->getMessage()];
        }

        return [true, $model];
    }

    public function updateDataAll($data, $id, $attribute = "id")
    {
        try {

            if (isset($data['id'])) unset ($data['id']);

            $model = $this->model->whereIn($attribute,$id)->update($data);

        } catch (\Exception $e) {
            return [false, $e->getMessage()];
        }

        return [true, $model];
    }

    public function deleteData($data)
    {
        try {

            $this->model->destroy($data);

        } catch (\Exception $e) {

            return [false, $e->getMessage()];
        }

        return [true, '数据已删除'];
    }

    /**
     * 关联关系
     * @param $getSql
     * @param int $type
     * @param string $function
     * @param array $data
     * @return array
     */
    public function saveRelation($getSql, int $type, string $function, array $data): array
    {
        try {

            if (!is_object($getSql)) {
                $list = $this->getData($getSql, 'find');
            } else {
                $list = $getSql;
            }

            switch ($type) {
                case 0://移除
                    if (!empty($data)) $list->$function()->detach($data);
                    break;
                case 1://增加
                    if (!empty($data)) $list->$function()->attach($data);
                    break;
                case 3: //修改
                    if (!empty($data)) $list->$function()->updateExistingPivot($data, ['deleted_at' => date("Y-m-d H:i:s")]);
                    break;
                default://同步
                    if (!empty($data)) $list->$function()->sync($data);
                    break;
            }
        } catch (\Exception $e) {
            return [false, $e->getMessage()];
        }

        return [true, '数据更新成功'];

    }
}
