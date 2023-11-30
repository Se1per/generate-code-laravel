<?php

namespace App\Lib\Repository\Eloquent;

use App\Lib\Repository\Eloquent\src\ModelFun;
use App\Lib\Repository\Exceptions\RepositoryException;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Container\Container as App;
use App\Lib\Repository\Contracts\CriteriaInterface;
use App\Lib\Repository\Criteria\Criteria;
use App\Lib\Repository\Contracts\RepositoryInterface;

/**
 * Class Repository
 * @package Bosnadev\Repositories\Eloquent
 */
abstract class Repository implements RepositoryInterface, CriteriaInterface
{
    //引入 方法层, 方便 可以直接Repository 直接调用 laravel orm 底层
    use ModelFun;

    /**
     * @var App
     */
    private $app;
    /**
     * @var
     */
    public $model;

    /**
     * @var Collection
     */
    protected $criteria;

    /**
     * @var bool
     */
    protected $skipCriteria = false;

    /**
     * Prevents from overwriting same criteria in chain usage
     * @var bool
     */
    protected $preventCriteriaOverwriting = true;

    /**
     *
     * @param App $app
     * @param Collection $collection
     * @throws RepositoryException
     */
    public function __construct()
    {
        $this->app = new App;
        $this->criteria = new Collection;
        $this->resetScope();
        $this->makeModel();
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public abstract function model();

    /**
     * @return \Illuminate\Database\Eloquent\Builder|Model
     * @throws RepositoryException
     */
    public function makeModel()
    {
        return $this->setModel($this->model());
    }

    /**
     * Set Eloquent Model to instantiate
     * @param $eloquentModel
     * @return \Closure|Model|mixed|object|null
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * User: Se1per
     * Date: 2023/8/2 18:57
     */
    public function setModel($eloquentModel)
    {
        $this->model = $this->app->make($eloquentModel);

        if (!$this->model instanceof Model)
            throw new RepositoryException("Class {$this->model} must be an instance of Illuminate\\Database\\Eloquent\\Model");

        return $this->model;
    }

    /**
     * @return $this
     */
    public function resetScope()
    {
        $this->skipCriteria(false);
        return $this;
    }

    /**
     * @param bool $status
     * @return $this
     */
    public function skipCriteria($status = true)
    {
        $this->skipCriteria = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @param Criteria $criteria
     * @return $this
     */
    public function getByCriteria(Criteria $criteria)
    {
        $this->model = $criteria->apply($this->model, $this);
        return $this;
    }

    /**
     * @param Criteria $criteria
     * @return $this
     */
    public function pushCriteria(Criteria $criteria)
    {
        if ($this->preventCriteriaOverwriting) {
            // Find existing criteria
            $key = $this->criteria->search(function ($item) use ($criteria) {
                return (is_object($item) && (get_class($item) == get_class($criteria)));
            });

            // Remove old criteria
            if (is_int($key)) {
                $this->criteria->offsetUnset($key);
            }
        }

        $this->criteria->push($criteria);
        return $this;
    }

    /**
     * @return $this
     */
    public function applyCriteria()
    {
        if ($this->skipCriteria === true)
            return $this;

        foreach ($this->getCriteria() as $criteria) {
            if ($criteria instanceof Criteria)
                $this->model = $criteria->apply($this->model, $this);
        }

        return $this;
    }
}
