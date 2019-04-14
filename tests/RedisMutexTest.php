<?php
/**
 * @link http://www.yiiframework.com/
 *
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yii\Mutex\Tests;

use Yii\Mutex\RedisMutex;

/**
 * Class PgsqlMutexTest.
 *
 * @group mutex
 * @group db
 * @group pgsql
 */
class RedisMutexTest
{
    use MutexTestTrait;

    protected static $mutexPrefix = 'prefix';

    /**
     * @return RedisMutex
     */
    protected function createMutex()
    {
        return new RedisMutex($this->getConnection(), self::$mutexPrefix);
    }

    private function getConnection()
    {
        // TODO: create MySQL connection here
    }
}
