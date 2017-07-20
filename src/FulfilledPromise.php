<?php

namespace PHPromise\Promise;

class FulfilledPromise extends PromiseResult
{
    function __construct($value)
    {
        parent::__construct($value, self::FULFILLED);
    }
}