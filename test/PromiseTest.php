<?php

namespace PHPromise\Test;

require __DIR__."/../vendor/autoload.php";

use PHPromise\Promise\FulfilledPromise;
use PHPromise\Promise\Promise;
use PHPromise\Promise\RejectedPromise;

class PromiseTest extends \PHPUnit_Framework_TestCase {

    public function testResolve()
    {
        $promise = new Promise(function ($resolve, $reject) {
            swoole_timer_after(100, function () use ($resolve) {
                $resolve(1);
            });
        });

        $promise->then(function ($response) {
            $this->assertEquals($response, 1);
            return new FulfilledPromise($response + 2);
        })->otherwise(function ($value) {
            var_dump($value);
        })->then(function($response) {
            var_dump($response);
            return $response;
        });
    }

    public function testReject()
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