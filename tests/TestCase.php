<?php

declare(strict_types=1);

namespace Yiisoft\Mutex\Redis\Tests;

use Predis\Client;
use Yiisoft\Mutex\Redis\RedisMutex;

use function md5;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    private ?Client $client = null;

    protected function tearDown(): void
    {
        $this->client = null;

        parent::setUp();
    }

    protected function client(): Client
    {
        if ($this->client === null) {
            $this->client = new Client([
                'host' => '127.0.0.1',
                'port' => 6379,
                'prefix' => 'yiitest',
            ]);
        }

        return $this->client;
    }

    protected function isFreeLock(string $name): bool
    {
        return $this
                ->client()
                ->exists(md5(RedisMutex::class . $name)) === 0;
    }
}
