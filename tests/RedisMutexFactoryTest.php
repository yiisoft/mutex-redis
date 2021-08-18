<?php

declare(strict_types=1);

namespace Yiisoft\Mutex\Redis\Tests;

use Yiisoft\Mutex\MutexInterface;
use Yiisoft\Mutex\Redis\RedisMutex;
use Yiisoft\Mutex\Redis\RedisMutexFactory;

final class RedisMutexFactoryTest extends TestCase
{
    public function testCreateAndAcquire(): void
    {
        $mutexName = 'testCreateAndAcquire';
        $factory = new RedisMutexFactory($this->client(), 60);
        $mutex = $factory->createAndAcquire($mutexName);

        $this->assertInstanceOf(MutexInterface::class, $mutex);
        $this->assertInstanceOf(RedisMutex::class, $mutex);

        $this->assertFalse($this->isFreeLock($mutexName));
        $this->assertFalse($mutex->acquire());
        $mutex->release();

        $this->assertTrue($this->isFreeLock($mutexName));
        $this->assertTrue($mutex->acquire());
        $this->assertFalse($this->isFreeLock($mutexName));

        $mutex->release();
        $this->assertTrue($this->isFreeLock($mutexName));
    }
}
