<?php
/**
 * Created by PhpStorm.
 * User: Se1per
 * Date: 2023/2/10
 * Time: 10:09
 */

namespace App\Lib\Repository\Eloquent\src;

use App\Lib\MyHelp\facade\MyHelpCode;
use ErrorException;
use Illuminate\Database\Eloquent\Model;

trait ModelFun
{
    /**
     * 执行构造数据层
     * @param $data
     * @param bool $needToArray
     * @param string $get
     * @param $dataCallBack
     * @return $this
     * @throws ErrorException
     * User: Se1per
     * Date: 2023/8/31 11:49
     */
    public function runningSql($data, bool $needToArray = false, string $get = 'get',&$dataCallBack = null)
    {
        foreach ($data as $k => $val) {
            if ($get == 'count' && $k == 'skip') continue;
            if (method_exists($this, $k)) {
                $this->$k($val);
            } else {
                throw new ErrorException("Class Repository object does not have a function Name: " . $k);
            }
        }

        if($get == 'chunk'){
            $this->$get($dataCallBack);
            return $this;
        }

        $list = $this->$get();

        switch ($get) {
            case 'find':
                if ($list && $needToArray) $list = $list->toArray();
                break;
            case 'get':
                if ($needToArray) $list = $list->toArray();
                break;

        }

        return $list;
    }

    /**
     * whereHas
     * @param $hasWhereName $关联关系
     * @param $func $闭包函数
     * @return $this
     */
    public function whereHas($hasWhereName, $func)
    {
        $this->model = $this->model->whereHas($hasWhereName, $func);

        return $this;
    }

    /**
     * whereDoesntHave (无结果查询)
     * @param $hasWhereName $关联关系
     * @param $func $闭包函数
     * @return $this
     */
    public function whereDoesntHave($hasWhereName, $func)
    {
        $this->model = $this->model->whereDoesntHave($hasWhereName, $func);

        return $this;
    }

    /**
     * orWhereDoesntHave
     * @param $hasWhereName $关联关系
     * @param $func $闭包函数
     * @return $this
     */
    public function orWhereDoesntHave($hasWhereName, $func)
    {
        $this->model = $this->model->orWhereDoesntHave($hasWhereName, $func);

        return $this;
    }

    /**
     * orWhereHas
     * @param $hasWhereName $关联关系
     * @param $func $闭包函数
     * @return $this
     */
    public function orWhereHas($hasWhereName, $func)
    {
        $this->model = $this->model->orWhereHas($hasWhereName, $func);

        return $this;
    }

    /**
     * has 至少关联一条
     * @param $hasWhereName $关联关系
     * @param $func $闭包函数
     * @return $this
     */
    public function has(...$hasWhereName)
    {
        $this->model = $this->model->has(...$hasWhereName);

        return $this;
    }

    /**
     * doesntHave  没有关联
     * @param $hasWhereName $关联关系
     * @param $func $闭包函数
     * @return $this
     */
    public function doesntHave(...$hasWhereName)
    {
        $this->model = $this->model->doesntHave(...$hasWhereName);

        return $this;
    }

    /**
     * orDoesntHave  没有关联
     * @param $hasWhereName $关联关系
     * @param $func $闭包函数
     * @return $this
     */
    public function orDoesntHave(...$hasWhereName)
    {
        $this->model = $this->model->orDoesntHave(...$hasWhereName);

        return $this;
    }

    /**
     * orWhere
     * @param $hasWhereName $关联关系
     * @param $func $闭包函数
     * @return $this
     */
    public function orWhere(...$hasWhereName)
    {
        $this->model = $this->model->orWhere(...$hasWhereName);

        return $this;
    }


    public function leftJoin($join)
    {
        $this->model = $this->model->leftJoin(...$join);
    }


    /**
     * 基类构造方法 with
     * @param $with
     * @return $this
     */
    public function with($with)
    {
        $this->model = $this->model->with($with);
        return $this;
    }

    /**
     * 基类构造方法 withCount
     * @param $with
     * @return $this
     */
    public function withCount($with)
    {
        $this->model = $this->model->withCount($with);
        return $this;
    }

    /**
     * 行锁 lockForUpdate
     * @param $with
     * @return $this
     */
    public function lockForUpdate()
    {
        $this->model = $this->model->lockForUpdate();
        return $this;
    }

    /**
     * whereIn
     * @param $data
     * @return $this
     */
    public function whereTime($data)
    {
        $this->model->whereTime(...$data);
        return $this;
    }

    /**
     * whereIn
     * @param $data
     * @return $this
     */
    public function whereIn($data)
    {
        if (!is_array($data[0])) {
            $this->model = $this->model->whereIn(...$data);
        } else {
            foreach ($data as $val) {
                $this->model = $this->model->whereIn(...$val);
            }
        }
        return $this;
    }

    /**
     * whereRaw
     * @param $data
     * @return $this
     */
    public function whereRaw($data)
    {
        $this->model->whereRaw($data);
        return $this;
    }

    /**
     * whereNotIn
     * @param $data
     * @return $this
     */
    public function whereNotIn($data)
    {
        $this->model = $this->model->whereNotIn(...$data);
        return $this;
    }

    /**
     * whereNull
     * @param $data
     * @return $this
     */
    public function whereNull($data)
    {
        $this->model = $this->model->whereIn($data);
        return $this;
    }

    /**
     * json 搜索
     * @param $data
     * @return $this
     */
    public function whereJsonContains($data)
    {
        $this->model = $this->model->whereJsonContains(...$data);
        return $this;
    }

    /**
     * whereNull
     * @param $data
     * @param $data
     * @return $this
     */
    public function whereNotNull($data)
    {
        $this->model = $this->model->whereNotNull($data);
        return $this;
    }

    /**
     * 基类构造方法 whereLike
     * @param $where
     * @return $this
     */
    public function whereLike($where)
    {
        $this->model = $this->model->where($where);

        return $this;
    }

    /**
     * 获取列
     * @param $column
     * @return $this
     */
    public function pluck($column)
    {
        $this->model = $this->model->pluck($column);

        return $this;
    }

    /**
     * 基类构造方法 where
     * @param $where
     * @return $this
     */
    public function where(...$where)
    {

        $this->model = $this->model->where(...$where);

        return $this;
    }

    /**
     * 基类构造方法 where
     * @param $where
     * @return $this
     */
    public function having(...$where)
    {
        $this->model = $this->model->having(...$where);

        return $this;
    }

    function is_two_dimensional_array($array): bool
    {
        return count($array) > 0 && is_array(reset($array));
    }

    /**
     * 比较子句
     * @param $where
     * @return $this
     */
    public function whereBetween($where)
    {
        $this->model = $this->model->whereBetween(...$where);

        return $this;
    }

    /**
     * 比较子句
     * @param $where
     * @return $this
     */
    public function whereDate($where)
    {

        $this->model = $this->model->whereDate(...$where);

        return $this;
    }

    /**
     * 比较子句
     * @param $where
     * @return $this
     */
    public function whereNotBetween($where)
    {
        $this->model = $this->model->whereNotBetween(...$where);

        return $this;
    }

    public function whereExists($where)
    {
        $this->model = $this->model->whereExists($where);

        return $this;
    }

    public function from($where)
    {
        $this->model = $this->model->whereExists($where);

        return $this;
    }

    /**
     *  基类构造方法 order
     * @param $order
     * @return $this
     */
    public function orderBy($order)
    {
        $this->model = $this->model->orderBy(...$order);

        return $this;
    }

    public function orderByWith($order)
    {
        $this->model = $this->model->orderByWith(...$order);

        return $this;
    }

    public function setBindings(array $bindings, $type = 'where')
    {
        $this->model = $this->model->setBindings($bindings, $type);

        return $this;
    }

    public function orderByRaw($order)
    {
        $this->model = $this->model->orderBy(...$order);

        return $this;
    }

    /**
     * 数据最后一个
     * @param string $latest
     * @return $this
     */
    public function latest($latest = 'created_at')
    {
        $this->model = $this->model->latest($latest);

        return $this;
    }

    /**
     * 分页
     * @param array $data
     * @return $this
     */
    public function skip(array $data)
    {
        $data = MyHelpCode::pageLimit($data);

        $this->model = $this->model->skip($data['page'])->take($data['limit']);

        return $this;
    }

    /**
     * select 方法
     * @param $select
     * @return $this
     */
    public function select(...$select)
    {
        $this->model = $this->model->select(...$select);

        return $this;
    }

    /**
     * addSelect 方法
     * @param $select
     * @return $this
     */
    public function addSelect(...$select)
    {
        $this->model = $this->model->addSelect(...$select);

        return $this;
    }

    /**
     * 查询列
     * @param $fields
     * @return $this
     */
    public function fields($fields)
    {
        $this->model = $this->model->select($fields);

        return $this;
    }

    /**
     * 悲观锁
     * @return $this
     */
    public function sharedLock()
    {
        $this->model->sharedLock();
        return $this;
    }

    /**
     * find
     * @return mixed
     * @throws ErrorException
     * User: Se1per
     * Date: 2023/8/22 9:41
     */
    public function find()
    {
        $list =  $this->model->first();
        $this->refresh();
        return $list;
    }

//    public function update($data)
//    {
//        return $this->model->update($data);
//    }

    /**
     * 游标
     * @return mixed
     * User: Se1per
     * Date: 2023/6/20 18:19
     */
    public function cursor()
    {
        return $this->model->cursor();
    }

    /**
     * get
     * @return mixed
     * @throws ErrorException
     * User: Se1per
     * Date: 2023/8/22 9:25
     */
    public function get()
    {
        $list = $this->model->get();
        $this->refresh();
        return $list;
    }



    /**
     * toArray
     * @return array
     */
    public function toArray()
    {
        return $this->model->toArray();
    }

    /**
     * toSql
     * @return mixed
     */
    public function toSql()
    {
        return $this->model->toSql();
    }

    /**
     * 获取执行
     * @return mixed
     */
    public function count()
    {
        return $this->model->count();
    }


    /**
     * 模型刷新
     * @return Model
     * @throws ErrorException
     */
    public function refresh()
    {
        $this->table = str_replace('Repository', '', class_basename(get_class($this)));

        $this->model = 'App\\Models\\' . $this->table;

        if (class_exists($this->model)) {
            $model = app($this->model);
            if (!$model instanceof Model)
                throw new ErrorException("Class {$this->model} must be an instance of Illuminate\\Database\\Eloquent\\Model");
            return $this->model = $model;
        }

        throw new ErrorException("Class {$this->table} must be an instance of Illuminate\\Database\\Eloquent\\Model");
    }

    /**
     * 主从复制 查询主库
     * @return $this
     * User: Se1per
     * Date: 2023/9/19 10:03
     */
    public function onWriteConnection()
    {
        $this->model->onWriteConnection();
        return $this;
    }

    /**
     * @param array $columns
     * @return mixed
     */
    public function all($columns = array('*'))
    {
        $this->applyCriteria();
        return $this->model->get($columns);
    }

    /**
     * @param array $relations
     * @return $this
     */
//    public function with(array $relations)
//    {
//        $this->model = $this->model->with($relations);
//        return $this;
//    }

    /**
     * @param  string $value
     * @param  string $key
     * @return array
     */
    public function lists($value, $key = null)
    {
        $this->applyCriteria();
        $lists = $this->model->lists($value, $key);
        if (is_array($lists)) {
            return $lists;
        }
        return $lists->all();
    }

    /**
     * @param int $perPage
     * @param array $columns
     * @return mixed
     */
    public function paginate($perPage = 25, $columns = array('*'))
    {
        $this->applyCriteria();
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * save a model without massive assignment
     *
     * @param array $data
     * @return bool
     */
    public function saveModel(array $data)
    {
        foreach ($data as $k => $v) {
            $this->model->$k = $v;
        }
        return $this->model->save();
    }

    /**
     * @param array $data
     * @param $id
     * @param string $attribute
     * @return mixed
     */
    public function update(array $data, $id, $attribute = "id")
    {
        return $this->model->where($attribute, '=', $id)->update($data);
    }

    /**
     * @param  array $data
     * @param  $id
     * @return mixed
     */
    public function updateRich(array $data, $id)
    {
        if (!($model = $this->model->find($id))) {
            return false;
        }

        return $model->fill($data)->save();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }


    /**
     * @param $attribute
     * @param $value
     * @param array $columns
     * @return mixed
     */
    public function findBy($attribute, $value, $columns = array('*'))
    {
        $this->applyCriteria();
        return $this->model->where($attribute, '=', $value)->first($columns);
    }

    /**
     * @param $attribute
     * @param $value
     * @param array $columns
     * @return mixed
     */
    public function findAllBy($attribute, $value, $columns = array('*'))
    {
        $this->applyCriteria();
        return $this->model->where($attribute, '=', $value)->get($columns);
    }

    /**
     * Find a collection of models by the given query conditions.
     *
     * @param array $where
     * @param array $columns
     * @param bool $or
     *
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public function findWhere($where, $columns = ['*'], $or = false)
    {
        $this->applyCriteria();

        $model = $this->model;

        foreach ($where as $field => $value) {
            if ($value instanceof \Closure) {
                $model = (!$or)
                    ? $model->where($value)
                    : $model->orWhere($value);
            } elseif (is_array($value)) {
                if (count($value) === 3) {
                    list($field, $operator, $search) = $value;
                    $model = (!$or)
                        ? $model->where($field, $operator, $search)
                        : $model->orWhere($field, $operator, $search);
                } elseif (count($value) === 2) {
                    list($field, $search) = $value;
                    $model = (!$or)
                        ? $model->where($field, '=', $search)
                        : $model->orWhere($field, '=', $search);
                }
            } else {
                $model = (!$or)
                    ? $model->where($field, '=', $value)
                    : $model->orWhere($field, '=', $value);
            }
        }
        return $model->get($columns);
    }
}
