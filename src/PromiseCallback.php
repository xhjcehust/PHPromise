<?php

namespace PHPromise\Promise;

class PromiseCallback
{
    const THEN = 0;
    const OTHERWISE = 1;

    /** @var  callable */
    private $fn;
    private $type;

    public function __construct($type, callable $fn)
    {
        $this->type = $type;
        $this->fn = $fn;
    }


    public function getFn()
    {
        return $this->fn;
    }

    public function getType()
    {
        return $this->type;
    }

}