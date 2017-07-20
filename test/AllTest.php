<?php
/**
 * Created by PhpStorm.
 * User: marsnowxiao
 * Date: 2017/6/16
 * Time: 上午10:21
 */

require "../vendor/autoload.php";

use PHPromise\Promise\Promise;
use function PHPromise\Promise\all;

class AllTest extends \PHPUnit_Framework_TestCase {
    public function testAllSyncResolve()
    {
        $promise1 = new Promise(function (callable $resolve, callable $reject) {
            $resolve(1);
        });

        $promise2 = new Promise(function (callable $resolve, callable $reject) {
            $resolve(2);
        });

        all([$promise1, $promise2])->then(function ($value) {
            $this->assertEquals($value, [1, 2]);
            return $value;
        })->otherwise(function ($value) {
            $this->assertTrue(false);
        });
    }

    public function testAllAsyncResolve()
    {
        $promise1 = new Promise(function (callable $resolve, callable $reject) {
            swoole_timer_after(100, function () use ($resolve) {
                $resolve(1);
            });
        });

        $promise2 = new Promise(function (callable $resolve, callable $reject) {
            swoole_timer_after(10, function () use ($resolve) {
                $resolve(2);
            });
        });

        all([$promise1, $promise2])->then(function ($value) {
            $this->assertEquals($value, [1, 2]);
        })->otherwise(function ($value) {
            $this->assertTrue(false);
        });
    }

    public function testSyncResolveReject()
    {
        $promise1 = new Promise(function (callable $resolve, callable $reject) {
            $resolve(1);
        });

        $promise2 = new Promise(function (callable $resolve, callable $reject) {
            $reject(2);
        });

        all([$promise1, $promise2])->then(function ($value) {
            $this->assertTrue(false);
        })->otherwise(function ($value) {
            var_dump($value);
            $this->assertEquals($value, 2);
        });
    }

    public function testAsyncResolveReject()
    {
        $promise1 = new Promise(function (callable $resolve, callable $reject) {
            swoole_timer_after(100, function () use ($resolve) {
                $resolve(1);
            });
        });

        $promise2 = new Promise(function (callable $resolve, callable $reject) {
            swoole_timer_after(10, function () use ($reject) {
                $reject(2);
            });
        });

        all([$promise1, $promise2])->then(function ($value) {
            $this->assertTrue(false);
        })->otherwise(function ($value) {
            var_dump($value);
            $this->assertEquals($value, 2);
        });
    }

    public function testAllSyncReject()
    {
        $promise1 = new Promise(function (callable $resolve, callable $reject) {
            $reject(1);
        });

        $promise2 = new Promise(function (callable $resolve, callable $reject) {
            $reject(2);
        });

        all([$promise1, $promise2])->then(function ($value) {
            $this->assertTrue(false);
        })->otherwise(function ($value) {
            var_dump($value);
        });
    }

    public function testAllAsyncReject()
    {
        $promise1 = new Promise(function (callable $resolve, callable $reject) {
            swoole_timer_after(100, function () use ($reject) {
                $reject(1);
            });
        });

        $promise2 = new Promise(function (callable $resolve, callable $reject) {
            swoole_timer_after(10, function () use ($reject) {
                $reject(2);
            });
        });

        all([$promise1, $promise2])->then(function ($value) {
            $this->assertTrue(false);
        })->otherwise(function ($value) {
            var_dump($value);
            $this->assertEquals($value, 2);
            return $value + 3;
        })->then(function ($value) {
            var_dump($value);
        });
    }
}
