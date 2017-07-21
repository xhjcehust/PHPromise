<?php

namespace PHPromise\Test;

use PHPromise\Promise\FulfilledPromise;
use PHPromise\Promise\Promise;

class PromiseTest extends \PHPUnit_Framework_TestCase {
    public function testSyncResolve()
    {
        $promise = new Promise(function ($resolve, $reject) {
            $resolve(1);
        });

        $promise->then(function ($value) {
            $this->assertEquals($value, 1);
            return new FulfilledPromise($value + 2);
        })->otherwise(function ($value) {
            $this->assertTrue(false);
        })->then(function($value) {
            $this->assertEquals($value, 3);
            return $value;
        });
    }

    public function testAsyncResolve()
    {
        $promise = new Promise(function ($resolve, $reject) {
            swoole_timer_after(100, function () use ($resolve) {
                $resolve(1);
            });
        });

        $promise->then(function ($value) {
            $this->assertEquals($value, 1);
            return new FulfilledPromise($value + 2);
        })->otherwise(function ($value) {
            $this->assertTrue(false);
        })->then(function($value) {
            $this->assertEquals($value, 3);
            return $value;
        });
    }

    public function testSyncReject()
    {
        $promise = new Promise(function ($resolve, $reject) {
            $reject(new \Exception("Reject Promise"));
        });

        $promise->then(function ($response) {
            $this->assertTrue(false);
            return $response + 2;
        })->otherwise(function (\Exception $e) {
            $this->assertEquals($e->getMessage(), "Reject Promise");
        });
    }

    public function testAsyncReject()
    {
        $promise = new Promise(function ($resolve, $reject) {
            swoole_timer_after(100, function () use ($reject) {
                $reject(new \Exception("Reject Promise"));
            });
        });

        $promise->then(function ($response) {
            $this->assertTrue(false);
            return $response + 2;
        })->otherwise(function (\Exception $e) {
            $this->assertEquals($e->getMessage(), "Reject Promise");
        });
    }
}