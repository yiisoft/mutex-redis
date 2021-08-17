<?php

declare(strict_types=1);

namespace Yiisoft\Mutex\Redis\Tests;

use InvalidArgumentException;
use ReflectionClass;
use RuntimeException;
use Yiisoft\Mutex\Redis\RedisMutex;

use function microtime;
use function sleep;
use function time;

final class RedisMutexTest extends TestCase
{
    public function testMutexAcquire(): void
    {
        $mutex = $this->createMutex('testMutexAcquire');

        $this->assertTrue($mutex->acquire());
        $mutex->release();
    }

    public function testThatMutexLockIsWorking(): void
    {
        $mutexOne = $this->createMutex('testThatMutexLockIsWorking');
        $mutexTwo = $this->createMutex('testThatMutexLockIsWorking');

        $this->assertTrue($mutexOne->acquire());
        $this->assertFalse($mutexTwo->acquire());
        $mutexOne->release();
        $mutexTwo->release();

        $this->assertTrue($mutexTwo->acquire());
        $mutexTwo->release();
    }

    public function testThatMutexLockIsWorkingOnTheSameComponent(): void
    {
        $mutex = $this->createMutex('testThatMutexLockIsWorkingOnTheSameComponent');

        $this->assertTrue($mutex->acquire());
        $this->assertFalse($mutex->acquire());

        $mutex->release();
        $mutex->release();
    }

    public function testTimeout(): void
    {
        $mutexName = __METHOD__;
        $mutexOne = $this->createMutex($mutexName);
        $mutexTwo = $this->createMutex($mutexName);

        $this->assertTrue($mutexOne->acquire());
        $microtime = microtime(true);
        $this->assertFalse($mutexTwo->acquire(1));
        $diff = microtime(true) - $microtime;
        $this->assertTrue($diff >= 1 && $diff < 2);
        $mutexOne->release();
        $mutexTwo->release();
    }

    public function testFreeLock(): void
    {
        $mutexName = 'testFreeLock';
        $mutex = $this->createMutex($mutexName);

        $mutex->acquire();
        $this->assertFalse($this->isFreeLock($mutexName));

        $mutex->release();
        $this->assertTrue($this->isFreeLock($mutexName));
    }

    public function testTtl(): void
    {
        $mutexName = 'testTtl';
        $mutex = $this->createMutex($mutexName, 1);

        $this->assertTrue($mutex->acquire());
        $this->assertFalse($this->isFreeLock($mutexName));

        sleep(2);

        $this->assertTrue($this->isFreeLock($mutexName));
    }

    public function testDestruct(): void
    {
        $mutexName = 'testDestruct';
        $mutex = $this->createMutex($mutexName);

        $this->assertTrue($mutex->acquire());
        $this->assertFalse($this->isFreeLock($mutexName));

        unset($mutex);

        $this->assertTrue($this->isFreeLock($mutexName));
    }

    public function testConstructorFailure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('TTL must be a positive number greater than zero, "0" is received.');

        $this->createMutex('testConstructorFailure', 0);
    }

    public function testReleaseFailure(): void
    {
        $mutexName = 'testReleaseFailure';
        $mutex = $this->createMutex($mutexName);

        $expired = (new ReflectionClass($mutex))->getProperty('expired');
        $expired->setAccessible(true);
        $expired->setValue($mutex, time() + 3600);
        $expired->setAccessible(false);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Unable to release lock \"$mutexName\".");

        $mutex->release();
    }

    private function createMutex(string $name, int $ttl = 30): RedisMutex
    {
        return new RedisMutex($name, $this->client(), $ttl);
    }
}
