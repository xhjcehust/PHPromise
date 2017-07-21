<?php

namespace PHPromise\Promise;

class Promise
{
    /** @var  PromiseResult */
    private $result;
    /** @var PromiseCallback[] */
    private $callbackList = [];

    public function __construct(callable $callback)
    {
        $callback([$this, "resolve"], [$this, "reject"]);
    }

    public function resolve($value = null)
    {
        if ($this->callbackList === []) {
            $this->result = new FulfilledPromise($value);
            return;
        }
        $expect = PromiseCallback::THEN;
        foreach ($this->callbackList as $callback) {
            $type = $callback->getType();
            $fn = $callback->getFn();
            if ($type === $expect) {
                if ($value instanceof PromiseResult) {
                    $value = $value->getValue();
                }
                $value = call_user_func($fn, $value);
                if ($value instanceof RejectedPromise) {
                    $expect = PromiseCallback::OTHERWISE;
                } else {
                    $expect = PromiseCallback::THEN;
                }
            }
        }

        $this->result = $value;
    }

    public function reject($value = null)
    {
        if ($this->callbackList === []) {
            $this->result = new RejectedPromise($value);
            return;
        }

        $expect = PromiseCallback::OTHERWISE;
        foreach ($this->callbackList as $callback) {
            $type = $callback->getType();
            $fn = $callback->getFn();
            if ($type === $expect) {
                if ($value instanceof PromiseResult) {
                    $value = $value->getValue();
                }
                $value = call_user_func($fn, $value);
                if ($value instanceof RejectedPromise) {
                    $expect = PromiseCallback::OTHERWISE;
                } else {
                    $expect = PromiseCallback::THEN;
                }
            }
        }

        $this->result = $value;
    }

    public function then(callable $resolve)
    {
        try {
            if ($this->result instanceof FulfilledPromise) {
                $value = $resolve($this->result->getValue());
                if ($value instanceof PromiseResult) {
                    $this->result = $value;
                } else {
                    $this->result = new FulfilledPromise($value);
                }
            } else if ($this->result == null) {
                $this->callbackList[] = new PromiseCallback(PromiseCallback::THEN, $resolve);
            }
        } catch (\Exception $e) {
            var_dump($e);
        }
        return $this;
    }

    public function otherwise(callable $reject)
    {
        try {
            if ($this->result instanceof RejectedPromise) {
                $value = $reject($this->result->getValue());
                if ($value instanceof PromiseResult) {
                    $this->result = $value;
                } else {
                    $this->result = new FulfilledPromise($value);
                }
            } else if ($this->result == null) {
                $this->callbackList[] = new PromiseCallback(PromiseCallback::OTHERWISE, $reject);
            }
        } catch (\Exception $e) {
            var_dump($e);
        }
        return $this;
    }

    /**
     * @return PromiseResult
     */
    public function getResult()
    {
        return $this->result;
    }
}