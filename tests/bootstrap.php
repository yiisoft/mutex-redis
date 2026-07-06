<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

if (getenv('GITHUB_ACTIONS') !== 'true' || yiiMutexRedisIsRedisAvailable()) {
    return;
}

$lock = fopen(sys_get_temp_dir() . '/yiisoft-mutex-redis.lock', 'c');

if ($lock === false) {
    throw new RuntimeException('Unable to open Redis bootstrap lock.');
}

flock($lock, LOCK_EX);

try {
    if (!yiiMutexRedisIsRedisAvailable()) {
        yiiMutexRedisStartRedis((string) (getenv('REDIS_VERSION') ?: '6'));
        yiiMutexRedisWaitForRedis();
    }
} finally {
    flock($lock, LOCK_UN);
    fclose($lock);
}

function yiiMutexRedisStartRedis(string $version): void
{
    if (!in_array($version, ['4', '5', '6'], true)) {
        throw new RuntimeException("Unsupported Redis version \"$version\".");
    }

    $command = sprintf(
        'docker run --rm --detach --publish 6379:6379 redis:%s',
        escapeshellarg($version)
    );

    exec($command, $output, $exitCode);

    if ($exitCode !== 0) {
        throw new RuntimeException(
            sprintf(
                "Unable to start Redis %s.\nCommand: %s\nOutput:\n%s",
                $version,
                $command,
                implode("\n", $output)
            )
        );
    }
}

function yiiMutexRedisWaitForRedis(): void
{
    for ($attempt = 0; $attempt < 60; $attempt++) {
        if (yiiMutexRedisIsRedisAvailable()) {
            return;
        }

        usleep(500_000);
    }

    throw new RuntimeException('Redis did not become available on 127.0.0.1:6379.');
}

function yiiMutexRedisIsRedisAvailable(): bool
{
    $socket = @fsockopen('127.0.0.1', 6379, $errorCode, $errorMessage, 0.2);

    if ($socket === false) {
        return false;
    }

    fclose($socket);

    return true;
}
