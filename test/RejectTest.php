<?php

namespace PHPromise\Test;

use PHPromise\Promise\Promise;

class RejectTest extends \PHPUnit_Framework_TestCase {

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
