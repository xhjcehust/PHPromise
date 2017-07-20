<?php

namespace PHPromise\Test;

use PHPromise\Promise\Promise;

class ResolveTest extends \PHPUnit_Framework_TestCase {
    public function testThen()
    {
        $promise = new Promise(function ($resolve, $reject) {
            swoole_timer_after(100, function () use ($resolve) {
                $resolve(1);
            });
        });

        $promise->then(function ($response) {
            $this->assertEquals($response, 1);
            return $response + 2;
        })->then(function ($response) {
            $this->assertEquals($response, 3);
        });
    }
}

