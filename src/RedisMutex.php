<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Mutex;

use Yiisoft\Db\Redis\Connection;
use yii\helpers\Yii;

/**
 * Redis Mutex implements a mutex component using [redis](http://redis.io/) as the storage medium.
 *
 * Redis Mutex requires redis version 2.6.12 or higher to work properly.
 *
 * It needs to be configured with a redis [[Connection]] that is also configured as an application component.
 * By default it will use the `redis` application component.
 *
 * To use redis Mutex as the application component, configure the application as follows:
 *
 * ```php
 * [
 *     'mutex' => [
 *         '__class' => \Yiisoft\Mutex\RedisMutex::class,
 *         'redis' => [
 *             'hostname' => 'localhost',
 *             'port' => 6379,
 *             'database' => 0,
 *         ]
 *     ],
 * ]
 * ```
 *
 * Or if you have configured the redis [[Connection]] as an application component, the following is sufficient:
 *
 * ```php
 * [
 *     'mutex' => [
 *         'class' => \Yiisoft\Db\Redis\Mutex::class,
 *         // 'redis' => 'redis' // id of the connection application component
 *     ],
 * ]
 * ```
 *
 * @see \Yiisoft\Mutex\Mutex
 * @see http://redis.io/topics/distlock
 *
 * @since 2.0.6
 */
class RedisMutex extends Mutex
{
    /**
     * @var int the number of seconds in which the lock will be auto released.
     */
    public $expire = 30;
    /**
     * @var string a string prefixed to every cache key so that it is unique. If not set,
     * it will use a prefix generated from [[Application::id]]. You may set this property to be an empty string
     * if you don't want to use key prefix. It is recommended that you explicitly set this property to some
     * static value if the cached data needs to be shared among multiple applications.
     */
    public $keyPrefix;
    /**
     * @var Connection the Redis [[Connection]] object.
     */
    protected $connection;

    /**
     * @var array Redis lock values. Used to be safe that only a lock owner can release it.
     */
    private $lockValues = [];


    public function __construct(Connection $connection, string $keyPrefix, bool $autoRelease = true)
    {
        parent::__construct($autoRelease);
        $this->connection = $connection;
        $this->keyPrefix = $keyPrefix;
    }

    /**
     * Acquires a lock by name.
     * @param string $name of the lock to be acquired. Must be unique.
     * @param int $timeout time (in seconds) to wait for lock to be released. Defaults to `0` meaning that method will
     * return false immediately in case lock was already acquired.
     * @return bool lock acquiring result.
     */
    protected function acquireLock(string $name, int $timeout = 0): bool
    {
        $key = $this->calculateKey($name);
        // FIXME
        $value = Yii::getApp()->security->generateRandomString(20);
        $waitTime = 0;
        while (!$this->connection->executeCommand('SET', [$key, $value, 'NX', 'PX', (int) ($this->expire * 1000)])) {
            $waitTime++;
            if ($waitTime > $timeout) {
                return false;
            }
            sleep(1);
        }
        $this->lockValues[$name] = $value;
        return true;
    }

    /**
     * Releases acquired lock. This method will return `false` in case the lock was not found or Redis command failed.
     * @param string $name of the lock to be released. This lock must already exist.
     * @return bool lock release result: `false` in case named lock was not found or Redis command failed.
     */
    protected function releaseLock(string $name): bool
    {
        static $releaseLuaScript = <<<LUA
if redis.call("GET",KEYS[1])==ARGV[1] then
    return redis.call("DEL",KEYS[1])
else
    return 0
end
LUA;
        if (!isset($this->lockValues[$name]) || !$this->connection->executeCommand('EVAL', [
                $releaseLuaScript,
                1,
                $this->calculateKey($name),
                $this->lockValues[$name]
            ])) {
            return false;
        }

        unset($this->lockValues[$name]);
        return true;
    }

    /**
     * Generates a unique key used for storing the mutex in Redis.
     * @param string $name mutex name.
     * @return string a safe cache key associated with the mutex name.
     */
    protected function calculateKey(string $name): string
    {
        return $this->keyPrefix . md5(json_encode([__CLASS__, $name]));
    }
}
