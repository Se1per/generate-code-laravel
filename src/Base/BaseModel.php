<?php
/**
 * Created by PhpStorm.
 * User: Se1per
 * Date: 2023/8/2 19:08
 */

namespace Japool\Genconsole\Base;

use Japool\Genconsole\Eloquent\ormCache\Traits\QueryCacheable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{

/*
    |--------------------------------------------------------------------------
    | Cache Stores
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the cache "stores" for your application as
    | well as their drivers. You may even define multiple stores for the
    | same cache driver to group types of items stored in your caches.
    |
    | Supported drivers: "apc", "array", "database", "file",
    |         "memcached", "redis", "dynamodb", "octane", "null"
    |--------------------------------------------------------------------------
*/
    //自动缓存
    //use QueryCacheable;
    //    public $cacheFor = 3600; //缓存时间/秒
    //    public $cacheDriver = 'redis'; //驱动引擎
    //自动更新
    //    protected static $flushCacheOnUpdate = true;
    //缓存标记 (定义在下级model中)
    //    public $cacheTags = [self::class];
    //缓存前缀名 (定义在下级model中)
    //    public $cachePrefix = 'MyProject';

    /**
     * 隐藏关联关系
     *
     * @var array
     */
    protected $hidden = ['pivot'];

    /**
     * 为数组 / JSON 序列化准备日期。
     *
     * @param \DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }
}
