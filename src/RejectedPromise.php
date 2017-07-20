<?php

namespace PHPromise\Promise;

class RejectedPromise extends PromiseResult
{
    function __construct($value)
    {
        parent::__construct($value, self::REJECTED);
    }
}