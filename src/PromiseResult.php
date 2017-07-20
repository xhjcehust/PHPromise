<?php

namespace PHPromise\Promise;

class PromiseResult
{
    const FULFILLED = 0;
    const REJECTED = 1;

    private $value;
    private $state;

    public function __construct($value, $state)
    {
        $this->value = $value;
        $this->state = $state;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
    }
}