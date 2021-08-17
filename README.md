<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://yiisoft.github.io/docs/images/yii_logo.svg" height="100px">
    </a>
    <h1 align="center">Yii Mutex Library - Redis Driver</h1>
    <br>
</p>

[![Latest Stable Version](https://poser.pugx.org/yiisoft/mutex-redis/v/stable.png)](https://packagist.org/packages/yiisoft/mutex-redis)
[![Total Downloads](https://poser.pugx.org/yiisoft/mutex-redis/downloads.png)](https://packagist.org/packages/yiisoft/mutex-redis)
[![Build status](https://github.com/yiisoft/mutex-redis/workflows/build/badge.svg)](https://github.com/yiisoft/mutex-redis/actions?query=workflow%3Abuild)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yiisoft/mutex-redis/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/mutex-redis/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/yiisoft/mutex-redis/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/mutex-redis/?branch=master)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyiisoft%2Fmutex-redis%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/yiisoft/mutex-redis/master)
[![static analysis](https://github.com/yiisoft/mutex-redis/workflows/static%20analysis/badge.svg)](https://github.com/yiisoft/mutex-redis/actions?query=workflow%3A%22static+analysis%22)
[![type-coverage](https://shepherd.dev/github/yiisoft/mutex-redis/coverage.svg)](https://shepherd.dev/github/yiisoft/mutex-redis)

This library provides a Redis mutex implementation for [yiisoft/mutex](https://github.com/yiisoft/mutex).

## Requirements

- PHP 7.4 or higher.

## Installation

The package could be installed with composer:

```shell
composer require yiisoft/mutex-redis --prefer-dist
```

## General usage

## Testing

### Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```shell
./vendor/bin/phpunit
```

### Mutation testing

The package tests are checked with [Infection](https://infection.github.io/) mutation framework with
[Infection Static Analysis Plugin](https://github.com/Roave/infection-static-analysis-plugin). To run it:

```shell
./vendor/bin/roave-infection-static-analysis-plugin
```

### Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/). To run static analysis:

```shell
./vendor/bin/psalm
```

## License

The Yii Redis mutex is free software. It is released under the terms of the BSD License.
Please see [`LICENSE`](./LICENSE.md) for more information.

Maintained by [Yii Software](https://www.yiiframework.com/).

## Support the project

[![Open Collective](https://img.shields.io/badge/Open%20Collective-sponsor-7eadf1?logo=open%20collective&logoColor=7eadf1&labelColor=555555)](https://opencollective.com/yiisoft)

## Follow updates

[![Official website](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](https://www.yiiframework.com/)
[![Twitter](https://img.shields.io/badge/twitter-follow-1DA1F2?logo=twitter&logoColor=1DA1F2&labelColor=555555?style=flat)](https://twitter.com/yiiframework)
[![Telegram](https://img.shields.io/badge/telegram-join-1DA1F2?style=flat&logo=telegram)](https://t.me/yii3en)
[![Facebook](https://img.shields.io/badge/facebook-join-1DA1F2?style=flat&logo=facebook&logoColor=ffffff)](https://www.facebook.com/groups/yiitalk)
[![Slack](https://img.shields.io/badge/slack-join-1DA1F2?style=flat&logo=slack)](https://yiiframework.com/go/slack)
