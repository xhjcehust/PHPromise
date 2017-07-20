<?php

namespace PHPromise\Test;

use PHPromise\Promise\Promise;
use function PHPromise\Promise\race;

class RaceTest extends \PHPUnit_Framework_TestCase {
    public function testSyncResolveRace()
    {
        $resolver1 = function (callable $resolve, callable $reject) {
            $resolve("resolve promise1");
        };

        $promise1 = new Promise($resolver1, null);

        $resolver2 = function (callable $resolve, callable $reject) {
            $resolve("resolve promise2");
        };

        $promise2 = new Promise($resolver2, null);

        race([$promise1, $promise2])->then(function ($value) {
            $this->assertEquals($value, "resolve promise1");
        });
    }

    public function testAsyncResolveRace()
    {
        $resolver1 = function (callable $resolve, callable $reject) {
            swoole_timer_after(100, function () use ($resolve) {
                $resolve("resolve promise1");
            });
        };

        $promise1 = new Promise($resolver1, null);

        $resolver2 = function (callable $resolve, callable $reject) {
            swoole_timer_after(1000, function () use ($resolve) {
                $resolve("resolve promise2");
            });
        };

        $promise2 = new Promise($resolver2, null);

        race([$promise1, $promise2])->then(function ($value) {
            $this->assertEquals($value, "resolve promise1");
        });
    }

    public function testAsyncRejectRace()
    {
        $resolver1 = function (callable $resolve, callable $reject) {
            swoole_timer_after(100, function () use ($reject) {
                $reject("reject promise");
            });
        };

        $promise1 = new Promise($resolver1, null);

        $resolver2 = function (callable $resolve, callable $reject) {
            swoole_timer_after(1000, function () use ($resolve) {
                $resolve("resolve promise");
            });
        };

        $promise2 = new Promise($resolver2, null);

        race([$promise1, $promise2])->then(function ($value) {
            $this->assertTrue(false);
        })->otherwise(function ($value) {
            $this->assertEquals($value, "reject promise");
        });
    }

}