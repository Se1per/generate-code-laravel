<?php
/**
 * Created by PhpStorm.
 * User: Se1per
 * Date: 2023/8/2 18:51
 */

namespace Japool\Genconsole\Criteria;

use Japool\Genconsole\Contracts\RepositoryInterface as Repository;

abstract class Criteria
{
    /**
     * @param $model
     * @param Repository $repository
     * @return mixed
     */
    public abstract function apply($model, Repository $repository);
}
