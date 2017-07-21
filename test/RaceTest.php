<?php

namespace PHPromise\Test;

use PHPromise\Promise\Promise;
use function PHPromise\Promise\race;

class RaceTest extends \PHPUnit_Framework_TestCase {
    public function testSyncResolveRace()
    {
        $promise1 = new Promise(function (callable $resolve, callable $reject) {
            $resolve(1);
        });

        $promise2 = new Promise(function (callable $resolve, callable $reject) {
            $resolve(2);
        });

        race([$promise1, $promise2])->then(function ($value) {
            $this->assertEquals($value, 1);
        });
    }

    public function testAsyncResolveRace()
    {
        $promise1 = new Promise(function (callable $resolve, callable $reject) {
            swoole_timer_after(100, function () use ($resolve) {
                $resolve(1);
            });
        });

        $promise2 = new Promise(function (callable $resolve, callable $reject) {
            swoole_timer_after(1000, function () use ($resolve) {
                $resolve(2);
            });
        });

        race([$promise1, $promise2])->then(function ($value) {
            $this->assertEquals($value, 1);
        });
    }

    public function testSyncResolveRejectRace()
    {
        $promise1 = new Promise(function (callable $resolve, callable $reject) {
            $reject(1);
        });

        $promise2 = new Promise(function (callable $resolve, callable $reject) {
            $resolve(2);
        });

        race([$promise1, $promise2])->then(function ($value) {
            $this->assertTrue(false);
        })->otherwise(function ($value) {
            $this->assertEquals($value, 1);
            return $value;
        });
    }

    public function testAsyncResolveRejectRace()
    {
        $promise1 = new Promise(function (callable $resolve, callable $reject) {
            swoole_timer_after(100, function () use ($reject) {
                $reject(1);
            });
        });

        $promise2 = new Promise(function (callable $resolve, callable $reject) {
            swoole_timer_after(1000, function () use ($resolve) {
                $resolve(2);
            });
        });

        race([$promise1, $promise2])->then(function ($value) {
            $this->assertTrue(false);
        })->otherwise(function ($value) {
            $this->assertEquals($value, 1);
            return $value;
        });
    }

    public function testSyncRejectRace()
    {
        $promise1 = new Promise(function (callable $resolve, callable $reject) {
            $reject(1);
        });

        $promise2 = new Promise(function (callable $resolve, callable $reject) {
            $reject(2);
        });

        race([$promise1, $promise2])->then(function ($value) {
            $this->assertTrue(false);
        })->otherwise(function ($value) {
            $this->assertEquals($value, 1);
        });
    }

    public function testAsyncRejectRace()
    {
        $promise1 = new Promise(function (callable $resolve, callable $reject) {
            swoole_timer_after(100, function () use ($reject) {
                $reject(1);
            });
        });

        $promise2 = new Promise(function (callable $resolve, callable $reject) {
            swoole_timer_after(1000, function () use ($reject) {
                $reject(2);
            });
        });

        race([$promise1, $promise2])->then(function ($value) {
            $this->assertTrue(false);
        })->otherwise(function ($value) {
            $this->assertEquals($value, 1);
            return $value + 2;
        });
    }
}